<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            // Normal facture fields
            $table->string('provider')->nullable()->after('user_id');
            $table->string('plan')->nullable()->after('provider');
            $table->decimal('monthly_cost', 10, 2)->nullable()->after('plan');
            $table->date('start_date')->nullable()->after('monthly_cost');
            $table->date('end_date')->nullable()->after('start_date');
            $table->text('notes')->nullable()->after('end_date');

            // ADSL facture fields
            $table->boolean('is_adsl')->default(false)->after('notes');
            $table->string('adsl_provider')->nullable()->after('is_adsl');
            $table->string('adsl_plan')->nullable()->after('adsl_provider');
            $table->decimal('adsl_monthly_cost', 10, 2)->nullable()->after('adsl_plan');
            $table->date('adsl_start_date')->nullable()->after('adsl_monthly_cost');
            $table->date('adsl_end_date')->nullable()->after('adsl_start_date');
            $table->text('adsl_notes')->nullable()->after('adsl_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn([
                'provider', 'plan', 'monthly_cost', 'start_date', 'end_date', 'notes',
                'is_adsl', 'adsl_provider', 'adsl_plan', 'adsl_monthly_cost',
                'adsl_start_date', 'adsl_end_date', 'adsl_notes'
            ]);
        });
    }
};
