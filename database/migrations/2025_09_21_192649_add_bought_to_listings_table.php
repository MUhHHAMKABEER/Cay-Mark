<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('listings', function (Blueprint $table) {
        $table->boolean('bought')->default(false)->after('auction_duration');
    });
}

public function down()
{
    Schema::table('listings', function (Blueprint $table) {
        $table->dropColumn('bought');
    });
}

};
