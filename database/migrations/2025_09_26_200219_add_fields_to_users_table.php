<?php
// database/migrations/2024_01_01_000000_add_fields_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('email');
            $table->string('nationality')->after('remember_token');
            $table->enum('island', [
                'New Providence', 'Grand Bahama', 'Abaco', 'Andros', 'Eleuthera',
                'Cat Island', 'Exuma', 'Long Island', 'San Salvador', 'Acklins',
                'Crooked Island', 'Mayaguana', 'Inagua', 'Rum Cay', 'Bimini'
            ])->after('nationality');
            $table->date('dob')->after('island');
            $table->enum('gender', ['Male', 'Female'])->after('dob');
            $table->boolean('marketing_opt_in')->default(false)->after('gender');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'nationality', 'island', 'dob', 'gender', 'marketing_opt_in']);
        });
    }
}
