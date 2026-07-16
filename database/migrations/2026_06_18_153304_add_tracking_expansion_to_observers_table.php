<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingExpansionToObserversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('observers', function (Blueprint $table) {
            $table->string('event_type', 50)->nullable()->after('event');
            $table->string('event_name', 100)->nullable()->after('event_type');
            $table->string('section', 100)->nullable()->after('uri');
            $table->string('device', 20)->nullable()->after('host');
            $table->string('session_id', 36)->nullable()->index()->after('event_name');
            $table->string('visitor_id', 36)->nullable()->index()->after('session_id');
            $table->string('page_view_id', 36)->nullable()->after('visitor_id');
            $table->string('page_type', 50)->nullable()->after('page_view_id');
            $table->string('referer_original')->nullable()->after('referer');
            $table->string('utm_source', 500)->nullable()->after('referer_original');
            $table->string('utm_medium', 500)->nullable()->after('utm_source');
            $table->string('utm_campaign', 500)->nullable()->after('utm_medium');
            $table->text('metadata')->nullable()->after('utm_campaign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observers', function (Blueprint $table) {
            $table->dropColumn([
                'event_type', 'event_name', 'section', 'device',
                'session_id', 'visitor_id', 'page_view_id', 'page_type',
                'referer_original', 'utm_source', 'utm_medium', 'utm_campaign',
                'metadata',
            ]);
        });
    }
}
