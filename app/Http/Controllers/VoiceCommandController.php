<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;

class VoiceCommandController extends Controller
{
    public function handleCommand(Request $request)
    {
        $command = strtolower($request->input('command'));
        $responseMessage = '';

        $messages = config('messages');

        if (array_key_exists($command, $messages['greetings'])) {
            $responseMessage = $messages['greetings'][$command];
        } elseif (array_key_exists($command, $messages['conversations'])) {
            $responseMessage = $messages['conversations'][$command];
        } elseif (strpos($command, "search on youtube") !== false) {
            $searchTerm = str_ireplace("search on youtube", '', $command);
            $searchTerm = trim($searchTerm);
            $searchUrl = 'https://www.youtube.com/results?search_query=' . urlencode($searchTerm);
            return response()->json(['message' => $messages['additionalCommands']['search on youtube'] . ' ' . $searchTerm, 'url' => $searchUrl]);
        } elseif (strpos($command, 'search on google') !== false) {
            $searchTerm = str_ireplace('search on google', '', $command);
            $searchTerm = trim($searchTerm);
            $searchUrl = 'https://www.google.com/search?q=' . urlencode($searchTerm);
            return response()->json(['message' => $messages['additionalCommands']['search on google'] . ' ' . $searchTerm, 'url' => $searchUrl]);
        } else {
            $searchUrl = 'https://www.google.com/search?q=' . urlencode($command);
            return response()->json(['message' => 'I didn\'t understand the command. Searching on Google for: ' . $command, 'url' => $searchUrl]);
        }

        return response()->json(['message' => $responseMessage]);
    }

    public function askQuestions(Request $request)
    {
        $result = Gemini::geminiPro()->generateCOntent($request->question);
        return view('chat',[
            'question' => $request->question,
            'answer' => $result->text()
        ]);
    }

    public function chat()
    {
        return view('chat');
    }

}
