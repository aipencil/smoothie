<?php

declare(strict_types=1);

use Aipencil\Smoothie\Install\ClaudeCode;
use Aipencil\Smoothie\Install\Codex;
use Aipencil\Smoothie\Install\Copilot;
use Aipencil\Smoothie\Install\Cursor;
use Aipencil\Smoothie\Install\Gemini;
use Aipencil\Smoothie\Install\OpenCode;
use Aipencil\Smoothie\Install\PhpStorm;
use Aipencil\Smoothie\Install\VSCode;

test('VSCode has correct properties', function (): void {
    $vscode = new VSCode;

    expect($vscode->name)->toBe('vscode')
        ->and($vscode->label)->toBe('VS Code')
        ->and($vscode->skillsPath)->toBe('.github/copilot/skills/filament-development')
        ->and($vscode->guidelinesPath())->toBe('.github/copilot-instructions.md');
});

test('PhpStorm has correct properties', function (): void {
    $phpstorm = new PhpStorm;

    expect($phpstorm->name)->toBe('phpstorm')
        ->and($phpstorm->label)->toBe('PhpStorm')
        ->and($phpstorm->skillsPath)->toBe('.junie/skills/filament-development')
        ->and($phpstorm->guidelinesPath())->toBe('.junie/guidelines.md');
});

test('Cursor has correct properties', function (): void {
    $cursor = new Cursor;

    expect($cursor->name)->toBe('cursor')
        ->and($cursor->label)->toBe('Cursor')
        ->and($cursor->skillsPath)->toBe('.cursor/skills/filament-development')
        ->and($cursor->guidelinesPath())->toBe('.cursor/rules/filament.md');
});

test('ClaudeCode has correct properties', function (): void {
    $claude = new ClaudeCode;

    expect($claude->name)->toBe('claude_code')
        ->and($claude->label)->toBe('Claude Code')
        ->and($claude->skillsPath)->toBe('.claude/skills/filament-development')
        ->and($claude->guidelinesPath())->toBe('CLAUDE.md');
});

test('Codex has correct properties', function (): void {
    $codex = new Codex;

    expect($codex->name)->toBe('codex')
        ->and($codex->label)->toBe('Codex')
        ->and($codex->skillsPath)->toBe('.codex/skills/filament-development')
        ->and($codex->guidelinesPath())->toBe('AGENTS.md');
});

test('Copilot has correct properties', function (): void {
    $copilot = new Copilot;

    expect($copilot->name)->toBe('copilot')
        ->and($copilot->label)->toBe('GitHub Copilot')
        ->and($copilot->skillsPath)->toBe('.github/copilot/skills/filament-development')
        ->and($copilot->guidelinesPath())->toBe('.github/copilot-instructions.md');
});

test('Gemini has correct properties', function (): void {
    $gemini = new Gemini;

    expect($gemini->name)->toBe('gemini')
        ->and($gemini->label)->toBe('Gemini')
        ->and($gemini->skillsPath)->toBe('.gemini/skills/filament-development')
        ->and($gemini->guidelinesPath())->toBe('GEMINI.md');
});

test('OpenCode has correct properties', function (): void {
    $opencode = new OpenCode;

    expect($opencode->name)->toBe('opencode')
        ->and($opencode->label)->toBe('OpenCode')
        ->and($opencode->skillsPath)->toBe('.opencode/skills/filament-development')
        ->and($opencode->guidelinesPath())->toBe('AGENTS.md');
});

test('CodeEnvironment::all returns all environments', function (): void {
    $environments = Aipencil\Smoothie\Install\CodeEnvironment::all();

    expect($environments)->toHaveCount(8)
        ->and($environments[0])->toBeInstanceOf(VSCode::class)
        ->and($environments[1])->toBeInstanceOf(PhpStorm::class)
        ->and($environments[2])->toBeInstanceOf(Cursor::class)
        ->and($environments[3])->toBeInstanceOf(ClaudeCode::class)
        ->and($environments[4])->toBeInstanceOf(Codex::class)
        ->and($environments[5])->toBeInstanceOf(Copilot::class)
        ->and($environments[6])->toBeInstanceOf(Gemini::class)
        ->and($environments[7])->toBeInstanceOf(OpenCode::class);
});

test('CodeEnvironment::byName returns correct environment', function (): void {
    $vscode = Aipencil\Smoothie\Install\CodeEnvironment::byName('vscode');
    $cursor = Aipencil\Smoothie\Install\CodeEnvironment::byName('cursor');

    expect($vscode)->toBeInstanceOf(VSCode::class)
        ->and($cursor)->toBeInstanceOf(Cursor::class);
});

test('CodeEnvironment::byName returns null for unknown environment', function (): void {
    $unknown = Aipencil\Smoothie\Install\CodeEnvironment::byName('unknown');

    expect($unknown)->toBeNull();
});
