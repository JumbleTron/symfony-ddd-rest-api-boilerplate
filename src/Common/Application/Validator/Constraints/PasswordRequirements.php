<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class PasswordRequirements extends Constraint
{
    public string $tooShortMessage = 'Your password must be at least {{length}} characters long.';
    public string $missingLettersMessage = 'Your password must include at least one letter.';
    public string $requireCaseDiffMessage = 'Your password must include both upper and lower case letters.';
    public string $missingNumbersMessage = 'Your password must include at least one number.';
    public string $missingSpecialCharacterMessage = 'Your password must contain at least one special character.';

    public int $minLength;
    public bool $requireLetters;
    public bool $requireCaseDiff;
    public bool $requireNumbers;
    public bool $requireSpecialCharacter;

    //@phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function __construct(
        $options = null,
        ?array $groups = null,
        $payload = null,
        ?int $minLength = 7,
        ?bool $requireLetters = true,
        ?bool $requireCaseDiff = true,
        ?bool $requireNumbers = true,
        ?bool $requireSpecialCharacter = false
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->minLength = $minLength ?? $this->minLength;
        $this->requireLetters = $requireLetters ?? $this->requireLetters;
        $this->requireCaseDiff = $requireCaseDiff ?? $this->requireCaseDiff;
        $this->requireNumbers = $requireNumbers ?? $this->requireNumbers;
        $this->requireSpecialCharacter = $requireSpecialCharacter ?? $this->requireSpecialCharacter;
    }
}
