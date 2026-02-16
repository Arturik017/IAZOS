<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('phone')->after('last_name');

            $table->string('district')->after('phone');   // raion
            $table->string('locality')->after('district'); // sat/oras
            $table->string('postal_code')->nullable()->after('locality');

            // nu mai folosim customer_name/customer_phone/customer_address, dar nu le ștergem acum
            // ca să nu strici datele existente
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'first_name','last_name','phone','district','locality','postal_code'
            ]);
        });
    }
};
