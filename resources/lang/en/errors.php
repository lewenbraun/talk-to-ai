<?php

declare(strict_types=1);

return [
    'message_send_failed' => 'Failed to send message to the chat. Please try again later.',
    'api_error' => 'Server at :url returned an error for :service: :status',
    'connection_error' => 'Could not connect to server at :url for :service. Please check the URL and ensure the server is running.',
    'url_not_configured' => 'API URL is not configured for :service for the current user.',
    'missing_connections' => 'Not all required connections are loaded.',
    'unsupported_ai_service' => 'Unsupported AI service: :service',
    'missing_llm_name' => 'Chat model is missing LLM name.',
    'expected_iterable_chat_stream' => 'Expected iterable chat stream.',
    'no_temporary_user' => 'No temporary user found or user is not temporary.',
    'invalid_credentials' => 'The provided credentials are not correct.',
    'authentication_failed' => 'Authentication failed.',
];
