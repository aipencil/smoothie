<?php

declare(strict_types=1);

namespace Aipencil\Smoothie\Install;

use Aipencil\Smoothie\Contracts\SupportSkills;
use Aipencil\Smoothie\Install\Concerns\RendersBladeGuidelines;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class SkillWriter
{
    use RendersBladeGuidelines;

    public const SUCCESS = 0;

    public const UPDATED = 1;

    public const FAILED = 2;

    public function __construct(
        protected SupportSkills $app,
        protected CodeEnvironment $environment
    ) {}

    public function write(Skill $skill): int
    {
        if (! $this->isValidSkillName($skill->name)) {
            throw new RuntimeException("Invalid skill name: {$skill->name}");
        }

        $targetPath = base_path($this->environment->skillsPath.'/'.$skill->name);

        $existed = is_dir($targetPath);

        if (! $this->copyDirectory($skill->path, $targetPath)) {
            return self::FAILED;
        }

        $this->updateGuidelinesFile($skill);

        return $existed ? self::UPDATED : self::SUCCESS;
    }

    /**
     * @param  Collection<string, Skill>  $skills
     * @return array<string, int>
     */
    public function writeAll(Collection $skills): array
    {
        return $skills
            ->mapWithKeys(fn (Skill $skill): array => [$skill->name => $this->write($skill)])
            ->all();
    }

    protected function copyDirectory(string $source, string $target): bool
    {
        if (! is_dir($source)) {
            return false;
        }

        if (! $this->ensureDirectoryExists($target)) {
            throw new RuntimeException("Failed to create directory: {$target}");
        }

        $finder = Finder::create()
            ->files()
            ->in($source)
            ->ignoreDotFiles(false);

        foreach ($finder as $file) {
            if (! $this->copyFile($file, $target)) {
                return false;
            }
        }

        return true;
    }

    protected function copyFile(SplFileInfo $file, string $targetDir): bool
    {
        $relativePath = $file->getRelativePathname();
        $targetFile = $targetDir.'/'.$relativePath;

        if (! $this->ensureDirectoryExists(dirname($targetFile))) {
            return false;
        }

        $isBladeFile = str_ends_with($relativePath, '.blade.php');
        if ($isBladeFile) {
            $renderedContent = mb_trim($this->renderBladeFile($file->getRealPath()));
            $replacedTargetFile = preg_replace('/\.blade\.php$/', '.md', $targetFile);

            if ($replacedTargetFile === null) {
                $replacedTargetFile = mb_substr($targetFile, 0, -10).'.md';
            }

            return file_put_contents($replacedTargetFile, $renderedContent) !== false;
        }

        return @copy($file->getRealPath(), $targetFile) !== false;
    }

    protected function ensureDirectoryExists(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }

        return @mkdir($path, 0755, true) !== false;
    }

    protected function isValidSkillName(string $name): bool
    {
        return preg_match('/^[a-z0-9_-]+$/i', $name) === 1;
    }

    protected function updateGuidelinesFile(Skill $skill): void
    {
        $guidelinesPath = base_path($this->environment->guidelinesPath());

        if (! file_exists($guidelinesPath)) {
            return;
        }

        $content = file_get_contents($guidelinesPath);
        if ($content === false) {
            return;
        }

        if (str_contains($content, "`{$skill->name}`")) {
            return;
        }

        // Look for the filament rules section and Skills Activation subsection
        $filamentPattern = '/(===\s*filament\/filament\s*rules\s*===)/is';
        $skillsPattern = '/(===\s*filament\/filament\s*rules\s*===.*?)(## Skills Activation.*?)(?===\s*\w+\/\w+|$)/is';

        if (! preg_match($filamentPattern, $content)) {
            return; // Filament rules section not found
        }

        // Check if Skills Activation subsection exists
        if (preg_match($skillsPattern, $content, $matches)) {
            // Skills Activation section exists, add skill to it
            $skillsSection = $matches[2];
        } else {
            // Skills Activation section doesn't exist, create it
            $skillsSection = "## Skills Activation\n\n";
            // Insert it after the filament rules section header
            $content = preg_replace(
                $filamentPattern,
                '$1'."\n\n".$skillsSection,
                $content,
                1
            );
        }

        // Now add the skill entry to the Skills Activation section
        $description = preg_replace('/\s+/', ' ', mb_trim($skill->description));
        $skillEntry = "- `{$skill->name}` — {$description}";

        // Add skill to Skills Activation section
        $skillsActivationPattern = '/(## Skills Activation\n)(.*?)(?=\n===|$)/is';

        if (preg_match($skillsActivationPattern, $content, $matches)) {
            $updatedSection = $matches[1].$matches[2]."\n".$skillEntry;
            $content = preg_replace($skillsActivationPattern, $updatedSection, $content, 1);
        } else {
            // Fallback: just append the skill entry
            $content = preg_replace(
                '/(## Skills Activation\n)/',
                '$1'.$skillEntry."\n",
                $content,
                1
            );
        }

        file_put_contents($guidelinesPath, $content);
    }
}
