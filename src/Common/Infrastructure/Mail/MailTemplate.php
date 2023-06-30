<?php

namespace App\Common\Infrastructure\Mail;

interface MailTemplate
{
    public function getSubject(): string;
    public function getName(): string;

    public function getParams(): array;

    public function getAttachments(): array;

    public function getRecipients(): array;

    public function getBccRecipients(): array;
}