<?php

declare(strict_types=1);

use Aipencil\Smoothie\Install\Skill;

test('skill creates with all properties', function (): void {
    $skill = new Skill(
        name: 'notifications',
        package: 'smoothie',
        path: '/path/to/skill',
        description: 'Designs and implements Filament v4 Notifications',
        custom: false,
    );

    expect($skill->name)->toBe('notifications')
        ->and($skill->package)->toBe('smoothie')
        ->and($skill->path)->toBe('/path/to/skill')
        ->and($skill->description)->toBe('Designs and implements Filament v4 Notifications')
        ->and($skill->custom)->toBeFalse();
});

test('skill defaults custom to false', function (): void {
    $skill = new Skill(
        name: 'actions',
        package: 'smoothie',
        path: '/path/to/actions',
        description: 'Designs and implements Filament v4 Actions',
    );

    expect($skill->custom)->toBeFalse();
});

test('skill can be marked as custom', function (): void {
    $skill = new Skill(
        name: 'my-custom-skill',
        package: 'user',
        path: '/path/to/custom',
        description: 'User custom skill',
        custom: true,
    );

    expect($skill->custom)->toBeTrue();
});

test('skill returns new instance with custom flag using withCustom', function (): void {
    $original = new Skill(
        name: 'forms',
        package: 'smoothie',
        path: '/path/to/skill',
        description: 'Forms skill',
    );

    $custom = $original->withCustom(true);

    expect($original->custom)->toBeFalse()
        ->and($custom->custom)->toBeTrue()
        ->and($custom)->not->toBe($original)
        ->and($custom->name)->toBe($original->name)
        ->and($custom->package)->toBe($original->package)
        ->and($custom->path)->toBe($original->path)
        ->and($custom->description)->toBe($original->description);
});

test('skill display name for built-in', function (): void {
    $skill = new Skill(
        name: 'actions',
        package: 'smoothie',
        path: '/tmp/test',
        description: 'Actions skill',
        custom: false,
    );

    expect($skill->displayName())->toBe('actions');
});

test('skill display name for custom shows asterisk', function (): void {
    $skill = new Skill(
        name: 'my-skill',
        package: 'user',
        path: '/tmp/test',
        description: 'Custom skill',
        custom: true,
    );

    expect($skill->displayName())->toBe('.ai/my-skill*');
});
