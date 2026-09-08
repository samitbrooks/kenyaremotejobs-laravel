<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A mutable flag, not a ledger row, is the right shape here — it's a
     * standing preference ("don't send me marketing mail"), not a record of
     * discrete events. Nullable so the timestamp itself carries "have they
     * ever unsubscribed" without a separate boolean.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('marketing_opt_out_at')->nullable()->after('subscribed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('marketing_opt_out_at');
        });
    }
};
