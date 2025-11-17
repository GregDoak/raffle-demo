<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Domain\ValueObject;

use App\RaffleDemo\Notification\Domain\Exception\InvalidSubjectException;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SubjectTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $value = 'subject';

        // Act
        $subject = Subject::fromString($value);

        // Assert
        self::assertSame($value, $subject->toString());
    }

    #[Test]
    public function it_fails_when_the_subject_is_empty(): void
    {
        // Arrange
        $value = '';

        // Act
        self::expectExceptionMessage(InvalidSubjectException::fromEmptySubject()->getMessage());

        Subject::fromString($value);

        // Assert
        self::fail();
    }
}
