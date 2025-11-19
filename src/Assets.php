<?php

namespace PressbooksFrontendTools;

use Exception;

use function Kucrut\Vite\enqueue_asset;
use function Kucrut\Vite\get_manifest;
use function Kucrut\Vite\register_asset;

class Assets
{
    public function __construct(public string $slug, public AssetType $type)
    {
    }

    public function enqueue(string $file, string $handle, $options = []): Assets
    {
        enqueue_asset($this->getIncludeDir()."/{$this->slug}/assets/dist", $file, [
            'handle' => $handle,
            ...$options,
        ]);

        return $this;
    }

    public function register(string $file, string $handle, $options = []): Assets
    {
        register_asset($this->getIncludeDir()."/{$this->slug}/assets/dist", $file, [
            'handle' => $handle,
            ...$options,
        ]);

        return $this;
    }

    public function getAssetPath(string $file): string
    {
        return "{$this->getIncludeUrl()}/{$this->slug}/{$file}";
    }

    public function getPath(string $file): string
    {
        return "{$this->getIncludeDir()}/{$this->slug}/assets/dist/{$file}";
    }

    /**
     * @throws Exception
     */
    public function getAssetUrl(string $file): string
    {
        $dist_dir = "{$this->getIncludeDir()}/{$this->slug}/assets/dist";
        $dist_url = $this->getIncludeUrl()."/{$this->slug}/assets/dist";

        $manifest = get_manifest($dist_dir);

        if ($manifest->is_dev) {
            return $manifest->data->origin.'/'.$file;
        }

        if (isset($manifest->data->{$file}->file)) {
            return $dist_url.'/'.$manifest->data->{$file}->file;
        }

        return $dist_url.'/'.$file;
    }

    private function getIncludeDir(): string
    {
        $includeDir = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : '';
        if ($this->type === AssetType::THEME) {
            $includeDir = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR.'/themes' : '';
        }

        return $includeDir;
    }

    private function getIncludeUrl(): string
    {
        $includeUrl = defined('WP_PLUGIN_URL') ? WP_PLUGIN_URL : '';
        if ($this->type === AssetType::THEME) {
            $includeUrl = get_theme_root_uri();
        }

        return $includeUrl;
    }
}
