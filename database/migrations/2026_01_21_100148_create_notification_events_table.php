<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscriber_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', ['open', 'link_click']);
            $table->timestamp('created_at')->useCurrent();

            // Index for efficient querying
            $table->index(['notification_id', 'event_type']);
            // Unique constraint for counting unique events per subscriber
            $table->unique(['notification_id', 'subscriber_id', 'event_type'], 'unique_subscriber_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_events');
    }
};
