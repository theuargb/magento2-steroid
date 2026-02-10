# Magento 2 Steroids

Performance and reliability steroids for Magento 2 — powered by neuron-ai.

## Overview

Steroids is an AI-powered module for Magento 2 that automatically detects, diagnoses, and heals runtime errors in your store. When an exception occurs, the module captures comprehensive context and uses AI to suggest and apply fixes, minimizing downtime and manual intervention.

## Features

- **Automatic Error Detection**: Intercepts exceptions at the HTTP layer
- **AI-Powered Healing**: Uses advanced LLM models to analyze and fix errors
- **Fallback Responses**: Provides graceful fallback content when healing isn't possible
- **Circuit Breaker**: Prevents excessive healing attempts for recurring errors
- **Comprehensive Logging**: Detailed healing attempt logs in admin panel
- **Homepage Snapshots**: Captures and caches homepage state for fallback scenarios
- **Configurable Safety Controls**: Fine-grained control over healing behavior and permissions

## Requirements

- Magento 2.4.x or higher
- PHP 8.1, 8.2, 8.3, or 8.4
- [neuron-ai](https://github.com/inspector-apm/neuron-ai) ^2.0

## Installation

### Via Composer (Recommended)

```bash
composer require theuargb/magento2-steroids
bin/magento module:enable Theuargb_Steroids
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### Manual Installation

1. Download the module and extract to `app/code/Theuargb/Steroids`
2. Run the following commands:

```bash
bin/magento module:enable Theuargb_Steroids
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Configuration

Navigate to **Stores > Configuration > Theuargb > Steroids** in the Magento Admin panel.

### General Settings

- **Enable Steroids**: Master switch to enable/disable the module
- **LLM Provider**: Choose your AI provider (OpenAI, Azure OpenAI, Anthropic, etc.)
- **API Key**: Your LLM provider API key
- **Model**: The AI model to use (e.g., gpt-4o, claude-3-5-sonnet)
- **Base URL**: Optional custom endpoint for API calls

### Agent Settings

- **Heal Timeout**: Maximum time (seconds) for healing attempts
- **Fallback Timeout**: Maximum time (seconds) for generating fallback responses
- **Fallback Cache TTL**: How long to cache fallback responses
- **Max Tool Calls**: Limit on agent tool invocations per healing attempt

### Safety Controls

- **Max Attempts Per Hour**: Circuit breaker threshold per error fingerprint
- **Max Concurrent Healings**: Prevent resource exhaustion
- **Disallowed Tool Actions**: Blacklist specific agent tools
- **Allow File Writes**: Enable/disable file modification capabilities

### URL Filters

Configure URL patterns and custom prompts for specific routes that need special healing instructions.

### Snapshot Settings

- **Cron Frequency**: How often to capture homepage snapshots for fallback scenarios

## Usage

Once configured, Steroids automatically monitors your Magento store. When an error occurs:

1. The module intercepts the exception
2. Captures comprehensive context (request, environment, code)
3. Sends to the AI agent for analysis
4. Agent suggests and optionally applies fixes
5. Returns either a healed response or graceful fallback
6. Logs the attempt in **System > Steroids > Healing Log**

## Admin Features

### Healing Log

View detailed logs of all healing attempts at **System > Steroids > Healing Log**:

- Error details and stack traces
- Agent reasoning and actions taken
- Success/failure status
- Performance metrics (tokens used, execution time)

### Manual Snapshot Capture

Capture a fresh homepage snapshot on-demand via the **Capture Snapshot Now** button in system configuration.

## Database Tables

- `steroids_attempt`: Healing attempt logs
- `steroids_homepage_snapshot`: Homepage snapshots for fallback responses

## Cron Jobs

- `steroids_capture_homepage_snapshot`: Periodic homepage snapshot capture

## License

This module is licensed under the [Open Software License 3.0 (OSL-3.0)](LICENSE).

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](#).

## Credits

Powered by [neuron-ai](https://github.com/inspector-apm/neuron-ai) - Advanced AI agent framework for PHP.
