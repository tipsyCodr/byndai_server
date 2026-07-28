<?php

namespace App\AI\Interface;

interface AIService
{
    /**
     * Send chat messages to AI service and return response.
     *
     * @param array $messages [['role' => 'user|assistant', 'content' => '...']]
     * @param array $options
     * @return string
     */
    public function chat(array $messages, array $options = []): string;
}
