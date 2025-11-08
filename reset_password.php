<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Reset password cho user ID 1
$user = App\Models\User::find(1);

if ($user) {
    $newPassword = 'password123';
    $user->password = bcrypt($newPassword);
    $user->save();
    
    echo "✅ Password đã được reset!\n";
    echo "📧 Email: {$user->email}\n";
    echo "🔑 Password mới: {$newPassword}\n";
    echo "\n";
    echo "Bạn có thể login với:\n";
    echo "Email: {$user->email}\n";
    echo "Password: {$newPassword}\n";
} else {
    echo "❌ Không tìm thấy user với ID 1\n";
}
