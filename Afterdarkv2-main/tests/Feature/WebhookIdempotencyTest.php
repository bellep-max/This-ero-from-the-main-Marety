<?php

namespace Tests\Feature;

use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_event_id_is_unique()
    {
        // Create a webhook event
        $eventId = 'evt_test_12345';
        
        WebhookEvent::create([
            'event_id' => $eventId,
            'provider' => 'paypal',
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'payload' => ['test' => 'data'],
            'status' => 'pending',
        ]);

        // Attempt to create duplicate webhook event with same event_id
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        WebhookEvent::create([
            'event_id' => $eventId,
            'provider' => 'paypal',
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'payload' => ['test' => 'data2'],
            'status' => 'pending',
        ]);
    }

    public function test_duplicate_webhook_events_are_prevented()
    {
        $eventId = 'evt_test_67890';
        
        // Create first webhook event
        $event1 = WebhookEvent::create([
            'event_id' => $eventId,
            'provider' => 'paypal',
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'payload' => ['amount' => 100],
            'status' => 'pending',
        ]);

        // Try to find existing event (simulating idempotency check)
        $existingEvent = WebhookEvent::where('event_id', $eventId)->first();
        
        $this->assertNotNull($existingEvent);
        $this->assertEquals($event1->id, $existingEvent->id);
        $this->assertEquals('pending', $existingEvent->status);
    }

    public function test_webhook_event_can_be_marked_as_processed()
    {
        $eventId = 'evt_test_processed';
        
        $event = WebhookEvent::create([
            'event_id' => $eventId,
            'provider' => 'paypal',
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'payload' => ['amount' => 100],
            'status' => 'pending',
        ]);

        // Mark as processed
        $event->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        $event->refresh();
        
        $this->assertEquals('processed', $event->status);
        $this->assertNotNull($event->processed_at);
    }

    public function test_webhook_idempotency_workflow()
    {
        $eventId = 'evt_test_workflow';
        
        // First webhook delivery - should create new record
        $existingEvent = WebhookEvent::where('event_id', $eventId)->first();
        $this->assertNull($existingEvent);
        
        $event = WebhookEvent::create([
            'event_id' => $eventId,
            'provider' => 'paypal',
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'payload' => ['amount' => 100],
            'status' => 'pending',
        ]);
        
        // Process the event
        $event->update(['status' => 'processed', 'processed_at' => now()]);
        
        // Second webhook delivery (duplicate) - should find existing processed event
        $duplicateCheck = WebhookEvent::where('event_id', $eventId)->first();
        
        $this->assertNotNull($duplicateCheck);
        $this->assertEquals('processed', $duplicateCheck->status);
        $this->assertEquals($event->id, $duplicateCheck->id);
        
        // Verify only one record exists
        $count = WebhookEvent::where('event_id', $eventId)->count();
        $this->assertEquals(1, $count);
    }
}
