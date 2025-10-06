<?php
// database/migrations/2024_01_01_000002_create_user_documents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('doc_type', ['passport', 'driver_license', 'national_id']);
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type');
            $table->integer('size');
            $table->boolean('verified')->default(false);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_documents');
    }
}
