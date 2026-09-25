<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Organization;
use App\Models\Venue;
use App\Models\Event;
use App\Models\Speaker;
use App\Models\TrainingRequest;
use App\Models\Track;
use App\Models\SessionType;
use App\Models\Session;
use App\Models\Sponsor;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\RegistrationForm;
use App\Models\RegistrationFormField;
use App\Models\Registration;
use App\Models\RegistrationFieldValue;
use App\Models\Attendee;
use App\Models\CheckIn;
use App\Models\Invoice;
use App\Models\Refund;
use App\Models\ActivityLog;
use App\Models\EventType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@lifescend.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'phone' => '+15551112222',
            'timezone' => 'America/New_York',
            'status' => 'active',
        ]);

        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@lifescend.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'phone' => '+15552223333',
            'timezone' => 'America/New_York',
            'status' => 'active',
        ]);

        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'customer@lifescend.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'phone' => '+15553334444',
            'timezone' => 'America/Los_Angeles',
            'status' => 'active',
        ]);

        $attendeeUser = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'phone' => '+15554445555',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@lifescend.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'phone' => '+15556667777',
            'timezone' => 'America/Chicago',
            'status' => 'active',
        ]);

        // 2. Create Organization
        $org = Organization::create([
            'name' => 'Lifescend.events',
            'slug' => 'lifescend-events',
            'logo' => 'https://via.placeholder.com/150',
            'branding_primary_color' => '#4f46e5', // Indigo 600
            'branding_secondary_color' => '#10b981', // Emerald 500
            'website' => 'https://lifescend.events',
            'email' => 'hello@lifescend.events',
            'phone' => '+15559998888',
            'address' => '100 Innovation Way, Suite 500, Boston, MA 02110',
            'settings' => [
                'allow_self_registration' => true,
                'require_approval' => false,
                'default_currency' => 'USD',
            ],
            'created_by' => $admin->id,
        ]);

        // 3. Organization Users Pivot
        $org->users()->attach($admin->id, ['role' => 'admin', 'status' => 'active']);
        $org->users()->attach($staff->id, ['role' => 'staff', 'status' => 'active']);
        $org->users()->attach($customer->id, ['role' => 'customer', 'status' => 'active']);
        $org->users()->attach($attendeeUser->id, ['role' => 'customer', 'status' => 'active']);
        $org->users()->attach($manager->id, ['role' => 'manager', 'status' => 'active']);

        // 3.5. Create Event Types
        EventType::create(['name' => 'Inhouse', 'slug' => 'inhouse', 'mode' => 'inhouse', 'is_system' => true]);
        EventType::create(['name' => 'Online', 'slug' => 'online', 'mode' => 'online', 'is_system' => true]);
        EventType::create(['name' => 'Conference', 'slug' => 'conference', 'mode' => 'inhouse']);
        EventType::create(['name' => 'Training', 'slug' => 'training', 'mode' => 'inhouse']);
        EventType::create(['name' => 'Workshop', 'slug' => 'workshop', 'mode' => 'inhouse']);
        EventType::create(['name' => 'Webinar', 'slug' => 'webinar', 'mode' => 'online']);
        EventType::create(['name' => 'Seminar', 'slug' => 'seminar', 'mode' => 'hybrid']);

        // 4. Create Venue
        $venue = Venue::create([
            'name' => 'Silicon Valley Convention Center',
            'address' => '150 Almaden Blvd',
            'city' => 'San Jose',
            'country' => 'United States',
            'postal_code' => '95113',
            'latitude' => 37.3325,
            'longitude' => -121.8906,
            'capacity' => 1500,
        ]);

        // 5. Create Events
        $event1 = Event::create([
            'name' => 'Advanced Agentic Coding & AI Summit 2026',
            'slug' => 'ai-summit-2026',
            'description' => 'A premier gathering of AI researchers, developers, and agents building the future of software engineering.',
            'start_date' => now()->addMonths(2)->setHour(9)->setMinute(0)->setSecond(0),
            'end_date' => now()->addMonths(2)->addDays(2)->setHour(18)->setMinute(0)->setSecond(0),
            'timezone' => 'America/Los_Angeles',
            'venue_id' => $venue->id,
            'event_type' => 'conference',
            'capacity' => 800,
            'logo' => 'https://via.placeholder.com/300x300',
            'cover_image' => 'https://via.placeholder.com/1200x400',
            'theme_primary' => '#4f46e5',
            'theme_secondary' => '#06b6d4',
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $admin->id,
        ]);

        $event2 = Event::create([
            'name' => 'PHP 8.4 & Laravel 13 Masterclass',
            'slug' => 'laravel-masterclass',
            'description' => 'Hands-on training event covering standard practices, architectural designs, and performance optimization.',
            'start_date' => now()->addMonths(3)->setHour(10)->setMinute(0)->setSecond(0),
            'end_date' => now()->addMonths(3)->setHour(17)->setMinute(0)->setSecond(0),
            'timezone' => 'America/New_York',
            'venue_id' => $venue->id,
            'event_type' => 'training',
            'capacity' => 100,
            'logo' => 'https://via.placeholder.com/300x300',
            'cover_image' => 'https://via.placeholder.com/1200x400',
            'theme_primary' => '#ef4444', // Red 500
            'theme_secondary' => '#f97316', // Orange 500
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $admin->id,
        ]);

        // 6. Create Speakers
        $speaker1 = Speaker::create([
            'event_id' => $event1->id,
            'name' => 'Taylor Otwell',
            'slug' => 'taylor-otwell',
            'email' => 'taylor@laravel.com',
            'phone' => '+15557778888',
            'bio' => 'Founder of the Laravel PHP Framework and general ecosystem builder.',
            'company' => 'Laravel LLC',
            'job_title' => 'CEO / Creator',
            'photo' => 'https://via.placeholder.com/150',
            'social_links' => [
                'twitter' => 'https://twitter.com/taylorotwell',
                'github' => 'https://github.com/taylorotwell',
            ],
        ]);

        $speaker2 = Speaker::create([
            'event_id' => $event1->id,
            'name' => 'Rasmus Lerdorf',
            'slug' => 'rasmus-lerdorf',
            'email' => 'rasmus@php.net',
            'phone' => '+15558889999',
            'bio' => 'Creator of the PHP programming language and early web pioneer.',
            'company' => 'PHP Foundation',
            'job_title' => 'Core Developer',
            'photo' => 'https://via.placeholder.com/150',
            'social_links' => [
                'twitter' => 'https://twitter.com/rasmus',
                'github' => 'https://github.com/rlerdorf',
            ],
        ]);

        // Speaker Event Pivot
        $event1->speakers()->attach($speaker1->id, ['featured' => true]);
        $event1->speakers()->attach($speaker2->id, ['featured' => true]);
        $event2->speakers()->attach($speaker1->id, ['featured' => false]);

        // 7. Create Training Requests (Training Module replacement)
        TrainingRequest::create([
            'user_id' => $customer->id,
            'event_id' => null,
            'title' => 'Docker & Kubernetes Deployment Training',
            'description' => 'We need an in-depth training on containerizing our Laravel application, configuring Redis/Horizon and deploying them on AWS EKS.',
            'training_type' => 'online',
            'preferred_date' => now()->addMonths(1)->toDateString(),
            'status' => 'pending',
        ]);

        TrainingRequest::create([
            'user_id' => $attendeeUser->id,
            'event_id' => $event2->id,
            'title' => 'Advanced Laravel Security Audit',
            'description' => 'Requesting an in-house training for our security team to identify vulnerabilities in our SaaS code and APIs.',
            'training_type' => 'inhouse',
            'preferred_date' => now()->addMonths(2)->toDateString(),
            'status' => 'approved',
        ]);

        // 8. Sessions & Tracks
        $track1 = Track::create([
            'event_id' => $event1->id,
            'name' => 'Technical Deep Dives',
            'color' => '#3b82f6', // blue
        ]);

        $track2 = Track::create([
            'event_id' => $event1->id,
            'name' => 'Business of AI',
            'color' => '#10b981', // green
        ]);

        $typeKeynote = SessionType::create(['name' => 'Keynote']);
        $typeWorkshop = SessionType::create(['name' => 'Workshop']);

        $session1 = Session::create([
            'event_id' => $event1->id,
            'title' => 'The Future of PHP in an AI-Driven World',
            'description' => 'Rasmus discusses how PHP compiles, adapts, and plays nicely with agentic pipelines.',
            'start_time' => now()->addMonths(2)->setHour(10)->setMinute(0),
            'end_time' => now()->addMonths(2)->setHour(11)->setMinute(0),
            'room' => 'Grand Ballroom',
            'capacity' => 500,
            'track_id' => $track1->id,
            'session_type_id' => $typeKeynote->id,
        ]);
        $session1->speakers()->attach($speaker2->id);

        $session2 = Session::create([
            'event_id' => $event1->id,
            'title' => 'Building SaaS at Scale with Laravel 13',
            'description' => 'Taylor walks through architectural decisions that scale to millions of orders.',
            'start_time' => now()->addMonths(2)->setHour(11)->setMinute(30),
            'end_time' => now()->addMonths(2)->setHour(12)->setMinute(30),
            'room' => 'Grand Ballroom',
            'capacity' => 500,
            'track_id' => $track1->id,
            'session_type_id' => $typeKeynote->id,
        ]);
        $session2->speakers()->attach($speaker1->id);

        // 9. Sponsors
        Sponsor::create([
            'event_id' => $event1->id,
            'name' => 'Stripe',
            'slug' => 'stripe',
            'logo' => 'https://via.placeholder.com/120x40',
            'website' => 'https://stripe.com',
            'description' => 'Financial infrastructure for the internet.',
            'level' => 'platinum',
        ]);

        Sponsor::create([
            'event_id' => $event1->id,
            'name' => 'Tailwind CSS',
            'slug' => 'tailwind-css',
            'logo' => 'https://via.placeholder.com/120x40',
            'website' => 'https://tailwindcss.com',
            'description' => 'A utility-first CSS framework.',
            'level' => 'gold',
        ]);

        // 10. Ticket Types
        $ticketType1 = TicketType::create([
            'event_id' => $event1->id,
            'name' => 'General Admission',
            'price' => 299.00,
            'currency' => 'USD',
            'quantity' => 500,
            'sold_count' => 2,
            'max_per_user' => 5,
            'sales_start' => now()->subMonth(),
            'sales_end' => now()->addMonths(2),
            'is_active' => true,
            'is_paid' => true,
        ]);

        $ticketType2 = TicketType::create([
            'event_id' => $event1->id,
            'name' => 'VIP Experience',
            'price' => 599.00,
            'currency' => 'USD',
            'quantity' => 100,
            'sold_count' => 0,
            'max_per_user' => 2,
            'sales_start' => now()->subMonth(),
            'sales_end' => now()->addMonths(2),
            'is_active' => true,
            'is_paid' => true,
        ]);

        $ticketType3 = TicketType::create([
            'event_id' => $event2->id,
            'name' => 'Training Pass',
            'price' => 149.00,
            'currency' => 'USD',
            'quantity' => 100,
            'sold_count' => 1,
            'max_per_user' => 1,
            'sales_start' => now()->subMonth(),
            'sales_end' => now()->addMonths(3),
            'is_active' => true,
            'is_paid' => true,
        ]);

        // 11. Create Orders, OrderItems, Tickets
        $order = Order::create([
            'user_id' => $customer->id,
            'event_id' => $event1->id,
            'total_amount' => 299.00,
            'currency' => 'USD',
            'status' => 'completed',
            'stripe_payment_intent_id' => 'pi_mock_12345',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'ticket_type_id' => $ticketType1->id,
            'quantity' => 1,
            'unit_price' => 299.00,
            'total_price' => 299.00,
        ]);

        $ticket = Ticket::create([
            'event_id' => $event1->id,
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'ticket_type_id' => $ticketType1->id,
            'qr_code' => 'TKT-AI2026-9A8B7C',
            'status' => 'active',
        ]);

        // 12. Registration Forms
        $regForm = RegistrationForm::create([
            'event_id' => $event1->id,
            'title' => 'AI Summit Attendee Survey',
            'is_active' => true,
        ]);

        $field1 = RegistrationFormField::create([
            'form_id' => $regForm->id,
            'label' => 'Dietary Restrictions',
            'type' => 'text',
            'is_required' => false,
            'order' => 1,
        ]);

        $field2 = RegistrationFormField::create([
            'form_id' => $regForm->id,
            'label' => 'T-Shirt Size',
            'type' => 'select',
            'options' => ['S', 'M', 'L', 'XL', 'XXL'],
            'is_required' => true,
            'order' => 2,
        ]);

        // Registrations & Field Values
        $reg = Registration::create([
            'form_id' => $regForm->id,
            'user_id' => $customer->id,
            'event_id' => $event1->id,
            'submitted_at' => now(),
        ]);

        RegistrationFieldValue::create([
            'registration_id' => $reg->id,
            'field_id' => $field1->id,
            'value' => 'Vegan',
        ]);

        RegistrationFieldValue::create([
            'registration_id' => $reg->id,
            'field_id' => $field2->id,
            'value' => 'XL',
        ]);

        // 13. Attendees & Check-Ins
        $attendee = Attendee::create([
            'event_id' => $event1->id,
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'status' => 'checked-in',
        ]);

        CheckIn::create([
            'event_id' => $event1->id,
            'ticket_id' => $ticket->id,
            'user_id' => $customer->id,
            'checked_in_by' => $staff->id,
            'device_info' => [
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)',
                'app_version' => '1.0.4',
            ],
            'checked_in_at' => now()->subHours(2),
        ]);

        $ticket->update(['checked_in_at' => now()->subHours(2), 'status' => 'used']);

        // 14. Invoices & Refunds
        Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-2026-0001',
            'pdf_path' => 'invoices/inv_2026_0001.pdf',
            'issued_at' => now()->subDays(1),
        ]);

        // 15. Activity Logs
        ActivityLog::create([
            'user_id' => $customer->id,
            'event_id' => $event1->id,
            'action' => 'ticket_purchased',
            'model_type' => Order::class,
            'model_id' => $order->id,
            'metadata' => ['total_amount' => 299.00],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(1),
        ]);

        ActivityLog::create([
            'user_id' => $staff->id,
            'event_id' => $event1->id,
            'action' => 'attendee_checked_in',
            'model_type' => CheckIn::class,
            'model_id' => 1,
            'metadata' => ['ticket_id' => $ticket->id],
            'ip_address' => '192.168.1.50',
            'created_at' => now()->subHours(2),
        ]);
    }
}
