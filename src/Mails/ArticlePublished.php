<?php

namespace Fuelviews\SabHeroArticle\Mails;

use Fuelviews\SabHeroArticle\Exceptions\CannotSendEmail;
use Fuelviews\SabHeroArticle\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticlePublished extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(private Post $post, private string $toEamil = '')
    {
    }

    public function envelope(): Envelope
    {
        if ($this->post->isNotPublished()) {
            throw CannotSendEmail::postNotPublished();
        }

        return new Envelope(
            to: $this->toEamil,
            subject: 'New Purchase Mail'
        );

    }

    public function content(): Content
    {
        return new Content(
            view: 'filament-article::mails.article-published',
            with: ['post' => $this->post]
        );
    }
}
