<?php

namespace WeLabs\PluginComposer\Contracts;

interface FileSystemContract {
    /**
     * Get the all files inside the given source.
     *
     * @param string $src The source to scan.
     * @return array Return all files inside the given path.
     */
    public function get_files( string $src ): array;

    /**
     * Copy all files from source file/directory to destination file/directory.
     *
     * @param  string $src  Source file or directory.
     * @param  string $dest Destination file or directory.
     * @return void
     */
    public function copy( string $src, string $dest ): void;

    /**
     * Compress to zip
     *
     * @param string $source The path of the source file.
     * @param string $destination The path of the expected zip file.
     * @return boolean Return true for success and false for failure.
     */
    public function zip( string $source, string $destination ): bool;

    /**
     * Replace all files content with given key value paired placeholders.
     *
     * @param  string $src          Source file or directory.
     * @param  array  $placeholders key value paired placeholders.
     * @return void
     */
    public function replace( string $src, array $placeholders ): void;

    /**
     * Rename the file to destination.
     *
     * @param  string $src  The source file.
     * @param  string $dest The destination file.
     * @return void
     */
    public function rename( string $src, string $dest ): bool;

    /**
     * Delete the file or directory.
     *
     * @param string $dir The path of file or directory.
     * @return boolean Return true for success and false for failure.
     */
    public function remove( string $dir ): bool;
}
