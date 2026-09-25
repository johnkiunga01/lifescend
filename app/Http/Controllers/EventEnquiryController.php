<?php

namespace App\Http\Controllers;

use App\Models\EventEnquiry;
use App\Models\EventEnquiryAttachment;
use App\Models\Event;
use App\Mail\AdminNotificationMail;
use App\Mail\EnquiryReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Organization;

class EventEnquiryController extends Controller
{
    private const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'zip'];

    private const MAX_ATTACHMENTS = 5;

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $data['name'] = strip_tags(trim($data['name']));
        $data['contact'] = strip_tags(trim($data['contact']));
        $data['company'] = $data['company'] ? strip_tags(trim($data['company'])) : null;
        $data['message'] = strip_tags(trim($data['message']));

        EventEnquiry::create($data);

        try {
            $notifyEmail = Organization::firstOrFail()->settings['notification_email'] ?? null;
            if ($notifyEmail) {
                $eventName = $data['event_id'] ? (Event::find($data['event_id'])?->name ?? 'Unknown') : 'General';
                Mail::to($notifyEmail)->send(new AdminNotificationMail(
                    subjectText: 'New Training Enquiry - ' . $eventName,
                    body: "Name: {$data['name']}\nContact: {$data['contact']}\nCompany: " . ($data['company'] ?? 'N/A') . "\nEvent: {$eventName}\n\nMessage:\n{$data['message']}",
                    actionUrl: route('manage.enquiries.index'),
                    actionText: 'View Enquiries',
                ));
            }
        } catch (\Exception $e) {}

        return back()->with('enquiry_success', 'Your enquiry has been submitted. We will contact you soon.');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');
        $sort = (string) $request->query('sort', 'newest');
        $eventFilter = (string) $request->query('event', 'all');
        $attachmentsOnly = $request->boolean('attachments');

        $query = EventEnquiry::with('event', 'attachments');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('event', fn ($e) => $e->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status === 'pending') {
            $query->whereNull('replied_at');
        } elseif ($status === 'replied') {
            $query->whereNotNull('replied_at');
        }

        if ($eventFilter !== 'all') {
            $query->where('event_id', (int) $eventFilter);
        }

        if ($attachmentsOnly) {
            $query->has('attachments');
        }

        match ($sort) {
            'oldest' => $query->oldest(),
            'name_az' => $query->orderBy('name')->orderByDesc('created_at'),
            'name_za' => $query->orderByDesc('name')->orderByDesc('created_at'),
            default => $query->latest(),
        };

        $enquiries = $query->get();

        $eventOptions = Event::whereIn('id', EventEnquiry::whereNotNull('event_id')->distinct()->pluck('event_id'))
            ->orderBy('name')
            ->pluck('name', 'id');

        $filters = [
            'search' => $search,
            'status' => $status,
            'sort' => $sort,
            'event' => $eventFilter,
            'attachments' => $attachmentsOnly,
        ];

        $selected = null;
        if ($request->filled('enquiry')) {
            $selected = $enquiries->firstWhere('id', (int) $request->query('enquiry'));
        }
        $selected ??= $enquiries->first();

        return view('manage.enquiries.index', compact('enquiries', 'selected', 'filters', 'eventOptions'));
    }

    public function show(int $id)
    {
        return redirect()->route('manage.enquiries.index', ['enquiry' => $id]);
    }

    public function reply(Request $request, int $id)
    {
        $enquiry = EventEnquiry::findOrFail($id);

        $data = $request->validate([
            'reply_message' => 'required|string|max:5000',
            'reply_email' => 'nullable|email|max:255',
            'attachments' => 'nullable|array|max:' . self::MAX_ATTACHMENTS,
            'attachments.*' => 'file|max:10240',
        ]);

        $newAttachments = [];
        $uploaded = $request->file('attachments', []);
        foreach ($uploaded as $file) {
            if ($file->isValid() && ($attachment = $this->storeAttachment($enquiry, $file))) {
                $newAttachments[] = $attachment;
            }
        }
        $skipped = count($uploaded) - count($newAttachments);

        $enquiry->update([
            'reply_message' => strip_tags(trim($data['reply_message'])),
            'replied_at' => now(),
        ]);

        $email = null;
        if ($request->filled('reply_email') && filter_var($request->reply_email, FILTER_VALIDATE_EMAIL)) {
            $email = $request->reply_email;
        } elseif (filter_var($enquiry->contact, FILTER_VALIDATE_EMAIL)) {
            $email = $enquiry->contact;
        }

        if ($email) {
            Mail::to($email)->send(new EnquiryReplyMail($enquiry, $enquiry->reply_message, $newAttachments));
            $msg = 'Reply sent to ' . $email;
        } else {
            $msg = 'Reply saved. No valid email address found for this enquiry.';
        }

        if ($newAttachments) {
            $msg .= ' (' . count($newAttachments) . ' file' . (count($newAttachments) === 1 ? '' : 's') . ' attached)';
        }
        if ($skipped > 0) {
            $msg .= ' ' . $skipped . ' file' . ($skipped === 1 ? ' was' : 's were') . ' skipped (unsupported type or too large).';
        }

        return redirect()->route('manage.enquiries.index', ['enquiry' => $enquiry->id])->with('success', $msg);
    }

    public function previewAttachment(int $id)
    {
        return $this->serveAttachment($id, inline: true);
    }

    public function downloadAttachment(int $id)
    {
        return $this->serveAttachment($id, inline: false);
    }

    private function serveAttachment(int $id, bool $inline): StreamedResponse
    {
        $attachment = EventEnquiryAttachment::with('enquiry')->findOrFail($id);
        $enquiry = $attachment->enquiry;

        $user = Auth::user();
        $role = $user?->organizations()->first()?->pivot?->role ?? 'customer';
        $isStaff = in_array($role, ['admin', 'manager', 'staff'], true);
        $isOwner = $user && strcasecmp((string) $enquiry?->contact, (string) $user->email) === 0;

        abort_unless($isStaff || $isOwner, 403);
        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        $mime = in_array($attachment->mime, self::allowedMimes(), true) ? $attachment->mime : 'application/octet-stream';
        if ($inline && ! in_array($mime, ['application/pdf', 'image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) {
            $inline = false;
        }

        $disposition = $inline ? 'inline' : 'attachment';
        $filename = $this->safeFilename($attachment->original_name);

        return Storage::disk('local')->response($attachment->path, $filename, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ], $disposition);
    }

    public function destroy(int $id)
    {
        $enquiry = EventEnquiry::with('attachments')->findOrFail($id);

        foreach ($enquiry->attachments as $attachment) {
            Storage::disk('local')->delete($attachment->path);
        }

        $enquiry->delete();

        return redirect()->route('manage.enquiries.index')->with('success', 'Enquiry deleted.');
    }

    private function storeAttachment(EventEnquiry $enquiry, \Illuminate\Http\UploadedFile $file): ?EventEnquiryAttachment
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return null;
        }

        $mime = $file->getMimeType() ?: 'application/octet-stream';
        if (! in_array($mime, self::allowedMimes(), true)) {
            return null;
        }

        $path = $file->storeAs(
            'enquiry-attachments/' . $enquiry->id,
            Str::random(40) . '.' . $extension,
            'local'
        );

        if (! $path) {
            return null;
        }

        return $enquiry->attachments()->create([
            'original_name' => $this->safeFilename($file->getClientOriginalName()),
            'path' => $path,
            'mime' => $mime,
            'size' => $file->getSize() ?: 0,
        ]);
    }

    private function safeFilename(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = preg_replace('/[^\w\s.\-()]+/u', '', $name) ?? '';
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');
        $name = ltrim($name, '.');

        return $name !== '' ? Str::limit($name, 150, '') : 'attachment';
    }

    private static function allowedMimes(): array
    {
        return [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/CDFV2',
            'application/x-ole-storage',
            'application/vnd.ms-office',
            'text/csv',
            'text/plain',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
        ];
    }
}
