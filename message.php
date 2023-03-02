<?php
require_once(__DIR__."/vendor/autoload.php");

$api_key = file_get_contents("api_key.txt");
$api_key = trim($api_key);

use Orhanerday\OpenAi\OpenAi;
use League\CommonMark\CommonMarkConverter;

header("Content-Type: application/json");

$previous_conversation = json_decode($_POST['context'] ?? "[]", true);
$message = $_POST['message'];

$openai = new OpenAi($api_key);

$prompt = "Format the response in Markdown format if there's code in it. Also always explain your answer in detail.\n";
$prompt .= "Use previous conversation as context:\n";

foreach ($previous_conversation as $entry) {
    $prompt .= "Question: " . $entry[0] . "\n";
    $prompt .= "Answer: " . $entry[1] . "\n\n";
}

$prompt .= "New Question: " . $message . "\n\nAnswer:\n\n";

$complete = json_decode($openai->completion([
    'model' => 'text-davinci-003',
    'prompt' => $prompt,
    'temperature' => 0.9,
    'max_tokens' => 2000,
    'top_p' => 1,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
    'stop' => [
        "\nNote:",
        "\nQuestion:"
    ]
]));

if (isset($complete->choices[0]->text)) {
    $text = str_replace("\\n", "\n", $complete->choices[0]->text);
} elseif (isset($complete->error->message)) {
    $text = $complete->error->message;
} else {
    $text = "Sorry, but I don't know how to answer that.";
}

$converter = new CommonMarkConverter();
$styled_text = $converter->convert($text);

echo json_encode([
    "message" => (string)$styled_text,
    "raw_message" => $text,
    "status" => "success",
]);