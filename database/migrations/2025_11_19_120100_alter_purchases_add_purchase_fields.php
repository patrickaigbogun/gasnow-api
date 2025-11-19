<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'invoice_no')) {
                $table->string('invoice_no')->unique()->after('id');
            }
            if (!Schema::hasColumn('purchases', 'delivery_address')) {
                $table->string('delivery_address', 500)->nullable()->after('invoice_no');
            }
            if (!Schema::hasColumn('purchases', 'cylinder_size_id')) {
                $table->foreignId('cylinder_size_id')->nullable()->constrained('cylinder_sizes')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchases', 'purchase_kg_id')) {
                $table->foreignId('purchase_kg_id')->nullable()->constrained('purchase_kgs')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchases', 'delivery_time_id')) {
                $table->foreignId('delivery_time_id')->nullable()->constrained('delivery_times')->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'delivery_time_id')) {
                $table->dropConstrainedForeignId('delivery_time_id');
            }
            if (Schema::hasColumn('purchases', 'purchase_kg_id')) {
                $table->dropConstrainedForeignId('purchase_kg_id');
            }
            if (Schema::hasColumn('purchases', 'cylinder_size_id')) {
                $table->dropConstrainedForeignId('cylinder_size_id');
            }
            if (Schema::hasColumn('purchases', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
            if (Schema::hasColumn('purchases', 'invoice_no')) {
                $table->dropColumn('invoice_no');
            }
        });
    }
};
