<?php
// ==========================================
// TELEGRAM BOT - ইনকাম জোন
// ==========================================

$bot_token = "8252246058:AAE51zYPDXgnDdgOCCZh7_YwM6d2WyYsjaI";
$admin_chat_id = "7694112804";

// Telegram থেকে ডেটা নিন
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    // Webhook সেটআপ চেক
    if (isset($_GET['setwebhook'])) {
        $webhook_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $webhook_url = str_replace('?setwebhook=1', '', $webhook_url);
        
        $url = "https://api.telegram.org/bot{$bot_token}/setWebhook?url={$webhook_url}";
        $response = file_get_contents($url);
        echo "✅ Webhook সেটআপ করা হয়েছে!<br>";
        echo "🔗 URL: " . $webhook_url . "<br>";
        echo "📩 রেসপন্স: " . $response;
        die();
    }
    
    echo "🤖 ইনকাম জোন বট সক্রিয়!<br>";
    echo "📌 Webhook সেটআপ করতে: <a href='?setwebhook=1'>এখানে ক্লিক করুন</a>";
    die();
}

// মেসেজ প্রসেস করুন
if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $text = isset($message['text']) ? $message['text'] : '';

    // শুধু অ্যাডমিন
    if ($chat_id != $admin_chat_id) {
        sendMessage($chat_id, "⛔ আপনি এই বট ব্যবহার করতে পারবেন না।");
        die();
    }

    // কমান্ড হ্যান্ডলার
    switch ($text) {
        case '/start':
            sendMessage($chat_id, getStartMessage());
            break;
        case '/users':
            sendMessage($chat_id, getUsers());
            break;
        case '/pending':
            sendMessage($chat_id, getPending());
            break;
        case '/approved':
            sendMessage($chat_id, getApproved());
            break;
        case '/rejected':
            sendMessage($chat_id, getRejected());
            break;
        case '/stats':
            sendMessage($chat_id, getStats());
            break;
        case '/help':
            sendMessage($chat_id, getHelp());
            break;
        default:
            sendMessage($chat_id, "❌ ভুল কমান্ড। /help দিন");
            break;
    }
}

// ==========================================
// ফাংশন
// ==========================================

function sendMessage($chat_id, $text) {
    global $bot_token;
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'Markdown'
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

function getStartMessage() {
    return "🤖 *ইনকাম জোন বট*\n\n" .
           "স্বাগতম! নিচের কমান্ড ব্যবহার করুন:\n\n" .
           "/users - 👥 সব ইউজার\n" .
           "/pending - ⏳ পেন্ডিং উইথড্র\n" .
           "/approved - ✅ অ্যাপ্রুভড\n" .
           "/rejected - ❌ রিজেক্টেড\n" .
           "/stats - 📊 পরিসংখ্যান\n" .
           "/help - 🆘 হেল্প";
}

function getUsers() {
    return "👥 *সব ইউজার*\n\n" .
           "👤 জন ডো 🆔 123456789 💰 ১,৫০০ ✅ সক্রিয়\n" .
           "👤 জেন স্মিথ 🆔 987654321 💰 ২,৩০০ ✅ সক্রিয়\n" .
           "👤 বব উইলসন 🆔 456789123 💰 ০ 🚫 ব্যান\n\n" .
           "📊 *মোট:* ৩ জন";
}

function getPending() {
    return "⏳ *পেন্ডিং উইথড্র*\n\n" .
           "👤 জন ডো 📱 ০১৭XXXXXXXX 💰 ১০,০০০\n" .
           "👤 জেন স্মিথ 📱 ০১৮XXXXXXXX 💰 ১৫,০০০\n\n" .
           "📌 *মোট:* ২টি";
}

function getApproved() {
    return "✅ *অ্যাপ্রুভড উইথড্র*\n\n" .
           "👤 মাইক জনসন 📱 ০১৯XXXXXXXX 💰 ১০,০০০\n" .
           "👤 সারাহ লি 📱 ০১৬XXXXXXXX 💰 ২০,০০০\n\n" .
           "📊 *মোট:* ২টি";
}

function getRejected() {
    return "❌ *রিজেক্টেড উইথড্র*\n\n" .
           "👤 ডেভিড ব্রাউন 📱 ০১৭XXXXXXXX 💰 ১০,০০০\n\n" .
           "📊 *মোট:* ১টি";
}

function getStats() {
    return "📊 *পরিসংখ্যান*\n\n" .
           "👤 মোট ইউজার: ৩\n" .
           "💰 মোট ব্যালেন্স: ৩,৮০০\n" .
           "📤 পেন্ডিং: ২\n" .
           "✅ অ্যাপ্রুভড: ২\n" .
           "❌ রিজেক্টেড: ১\n" .
           "🚫 ব্যানড: ১";
}

function getHelp() {
    return "🆘 *কমান্ডের তালিকা*\n\n" .
           "/users - 👥 সব ইউজার\n" .
           "/pending - ⏳ পেন্ডিং\n" .
           "/approved - ✅ অ্যাপ্রুভড\n" .
           "/rejected - ❌ রিজেক্টেড\n" .
           "/stats - 📊 পরিসংখ্যান\n" .
           "/help - 🆘 হেল্প";
}
?>
