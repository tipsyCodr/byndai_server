use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Table already exist check
        if (!Schema::hasTable('transactions')) {

            Schema::create('transactions', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('plan_id')->nullable();

                // After success subscription link
                $table->unsignedBigInteger('subscription_id')->nullable();

                // Payment details
                $table->string('payment_gateway', 50)->nullable(); // razorpay, stripe, paypal
                $table->string('payment_method', 50)->nullable(); // card, upi, wallet

                $table->string('gateway_order_id', 100)->nullable();
                $table->string('gateway_payment_id', 100)->nullable();
                $table->text('gateway_signature')->nullable();

                $table->decimal('amount', 10, 2)->default(0);
                $table->string('currency', 10)->default('INR');

                $table->enum('status', ['pending', 'success', 'failed'])->default('pending');

                $table->json('raw_response')->nullable();

                $table->timestamps();

                // 🔥 Indexes (performance ke liye important)
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
};