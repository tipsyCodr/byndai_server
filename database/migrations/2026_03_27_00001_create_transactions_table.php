use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {

            Schema::create('transactions', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->unsignedBigInteger('subscription_id')->nullable();

                $table->string('payment_gateway', 50)->nullable();
                $table->string('payment_method', 50)->nullable();

                $table->string('gateway_order_id', 100)->nullable();
                $table->string('gateway_payment_id', 100)->nullable();
                $table->text('gateway_signature')->nullable();

                $table->decimal('amount', 10, 2)->default(0);
                $table->string('currency', 10)->default('INR');

                $table->enum('status', ['pending', 'success', 'failed'])->default('pending');

                $table->json('raw_response')->nullable();

                $table->timestamps();

                $table->index('user_id');
                $table->index('plan_id');
                $table->index('subscription_id');
                $table->index('status');
            });

        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
}