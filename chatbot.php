<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Normalize user input
    $message = strtolower(trim($_POST['message']));
    $message = preg_replace('/[^\w\s]/', '', $message); // remove punctuation

    $response = "❓ Sorry, I’m not sure how to answer that yet. 
    Try asking about *medicines*, *stock levels*, *reports*, or *pharmacy details*.";

    // Greeting
    if (preg_match('/\b(hi|hello|hey|good morning|good evening|how are you|whats up)\b/', $message)) {
        $response = "👋 Hello! How can I assist you today?";
    } 
    // Medicines
    elseif (preg_match('/\b(medicine|medicines|drug|drugs|tablet|tablets|pill|pills|available medicines|list of medicines)\b/', $message)) {
        $response = "💊 You can view available medicines in the *Medicines section*. Want me to guide you?";
    } 
    // Stock / Inventory
    elseif (preg_match('/\b(stock|inventory|low stock|out of stock|current stock|show inventory)\b/', $message)) {
        $response = "📦 Currently, 5 medicines are running low on stock. Check the *Inventory section* for full details.";
    } 
    // Expiry
    elseif (preg_match('/\b(expiry|expiring|expired|near expiry|check expiry|show expiry report)\b/', $message)) {
        $response = "⚠️ There are 3 medicines expiring soon. Please check the *Expiry Reports*.";
    } 
    // Reports
    elseif (preg_match('/\b(report|reports|sales report|inventory report|statistics|performance|generate report|show report)\b/', $message)) {
        $response = "📊 You can generate Sales & Inventory Reports under the *Reports* section.";
    } 
    // Pharmacy info
    elseif (preg_match('/\b(opening hours|working time|business hours|contact|help|support|phone number|email)\b/', $message)) {
        $response = "ℹ️ Our pharmacy is open from 8:00 AM - 8:00 PM. For help, check the *Contact Us* page.";
    } 
    // Thank you
    elseif (preg_match('/\b(thank you|thanks|appreciate|grateful)\b/', $message)) {
        $response = "😊 You're welcome! Always here to help.";
    } 
    // Goodbye
    elseif (preg_match('/\b(bye|goodbye|see you|take care|later)\b/', $message)) {
        $response = "👋 Goodbye! Have a wonderful day.";
    }

    echo $response;
}
?>
