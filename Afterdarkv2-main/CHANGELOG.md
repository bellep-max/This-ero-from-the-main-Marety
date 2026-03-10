# Changelog - Code Review Fixes

## Security Improvements

### Session Security
- Enabled session encryption by default
- Enabled secure cookies (HTTPS-only) by default
- Added HTTP-only flag to prevent JavaScript access

### Rate Limiting
- Added rate limiting to all API routes (60 requests per minute)
- Protects against API abuse and DDoS attacks

### Webhook Security
- Implemented webhook idempotency tracking to prevent duplicate processing
- Added webhook event database table with status tracking
- Enhanced error handling and logging for webhook failures

## Bug Fixes

### Subscription Scanning
- Fixed early-return bug in subscription scan command
- Command now processes all subscriptions instead of stopping after the first one
- Improved error handling to log errors and continue processing

### Vue 3 Compatibility
- Fixed lifecycle hook (beforeDestroy → beforeUnmount)
- Fixed variable declarations and reactive property access

## Performance Optimizations

### Database Indexes
- Added composite indexes on songs table for user queries and visibility filters
- Added composite indexes on orders table for payment status queries
- Added composite indexes on subscriptions table for billing queries
- Added composite indexes on activities and comments for timeline queries

### Caching
- Configured Redis as default cache driver (replacing database)
- Configured Redis as default session driver (replacing database)
- Configured Redis as default queue driver (replacing database)
- Added HTTP caching headers middleware with ETag and Last-Modified support

## New Features

### Media Asset Management
- Created media_assets table for master-rendition relationships
- Support for polymorphic relationships via assetable_type/id
- Track format, quality, bitrate, sample_rate, codec metadata
- Status tracking for transcode job progress

### FFmpeg Transcoding
- Created TranscodeMediaJob with retry logic (3 attempts with exponential backoff)
- Comprehensive audit logging for job lifecycle
- Support for multiple audio formats (mp3, aac, flac) with quality settings
- Automatic rendition record creation on successful transcode

## Configuration

### Production Defaults
- Updated .env.example with production-ready defaults
- Set APP_ENV to production and APP_DEBUG to false
- Enabled PayPal SSL validation
- Configured Redis for cache/session/queue

## Files Changed
- 12 new migrations
- 3 new models (WebhookEvent, MediaAsset)
- 1 new job (TranscodeMediaJob)
- 1 new middleware (AddCacheHeaders)
- 5 configuration files updated
- 1 bug fix in SubscriptionCommand
- 1 bug fix in PayPalWebhook
- 3 Vue component fixes
