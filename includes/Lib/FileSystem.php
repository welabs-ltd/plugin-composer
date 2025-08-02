<?php

namespace WeLabs\PluginComposer\Lib;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WeLabs\PluginComposer\Contracts\FileSystemContract;
use ZipArchive;

class FileSystem implements FileSystemContract {

    /**
     * @inheritDoc
     */
    public function copy( string $src, string $dest ): void {
        // Validate paths
        $this->validate_path( $src );
        $this->validate_path( $dest );

        $files = $this->get_files( $src );

        foreach ( $files as $file_path ) {
            $dest_path = str_replace( $src, $dest, $file_path );
            $dir = dirname( $dest_path );

            if ( ! is_dir( $dir ) ) {
                if ( ! mkdir( $dir, 0755, true ) ) {
                    throw new \RuntimeException( 'Failed to create directory: ' . $dir );
                }
            }

            if ( ! copy( $file_path, $dest_path ) ) {
                throw new \RuntimeException( 'Failed to copy file: ' . $file_path );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function rename( string $src, string $dest ): bool {
        // Validate paths
        $this->validate_path( $src );
        $this->validate_path( $dest );

        return rename( $src, $dest );
    }

    /**
     * @inheritDoc
     */
    public function replace( string $src, array $patterns ): void {
        $files = $this->get_files( $src );

        foreach ( $files as $file ) {
            $content = file_get_contents( $file );
            if ( false === $content ) {
                throw new \RuntimeException( 'Failed to read file: ' . $file );
            }

            foreach ( $patterns as $search => $value ) {
                $content = str_replace( $search, $value, $content );
            }

            if ( false === file_put_contents( $file, $content ) ) {
                throw new \RuntimeException( 'Failed to write file: ' . $file );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function get_files( string $src ): array {
        // Validate path
        $this->validate_path( $src );

        if ( ! is_dir( $src ) && is_file( $src ) ) {
            return (array) $src;
        }

        $files = [];

        $iterator = new FilesystemIterator( $src );

        foreach ( $iterator as $entry ) {
            $relative_path = $entry->getFilename();
            $name = $src . '/' . $relative_path;
            if ( ! is_dir( $name ) ) {
                $files[] = $name;
            } else {
                array_push( $files, ...$this->get_files( $name, $files ) );
            }
        }

        return $files;
    }

    /**
     * @inheritDoc
     */
    public function zip( string $source, string $destination ): bool {
        // Validate paths
        $this->validate_path( $source );
        $this->validate_path( $destination );

        if ( ! extension_loaded( 'zip' ) || ! file_exists( $source ) ) {
            return false;
        }

        $zip = new ZipArchive();
        if ( ! $zip->open( $destination, ZIPARCHIVE::CREATE ) ) {
            return false;
        }

        $source = str_replace( '\\', '/', realpath( $source ) );

        if ( is_dir( $source ) === true ) {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source ), RecursiveIteratorIterator::SELF_FIRST );
            foreach ( $files as $file ) {
                $file = str_replace( '\\', '/', $file );

                if ( in_array( substr( $file, strrpos( $file, '/' ) + 1 ), array( '.', '..' ), true ) ) {
                    continue;
                }

                $file = realpath( $file );

                if ( is_dir( $file ) === true ) {
                    $zip->addEmptyDir( str_replace( $source . '/', '', $file . '/' ) );
                } elseif ( is_file( $file ) === true ) {
                    $content = file_get_contents( $file );
                    if ( false === $content ) {
                        continue; // Skip files that can't be read
                    }
                    $zip->addFromString( str_replace( $source . '/', '', $file ), $content );
                }
            }
        } elseif ( is_file( $source ) === true ) {
            $content = file_get_contents( $source );
            if ( false !== $content ) {
                $zip->addFromString( basename( $source ), $content );
            }
        }

        return $zip->close();
    }

    /**
     * @inheritDoc
     */
    public function remove( string $dir ): bool {
        // Validate path
        $this->validate_path( $dir );

        $files = array_diff( scandir( $dir ), array( '.', '..' ) );

        foreach ( $files as $file ) {
            $path = "$dir/$file";
            if ( is_dir( $path ) ) {
                $this->remove( $path );
            } else {
                unlink( $path );
            }
        }

        return rmdir( $dir );
    }

    /**
     * Validate file path for security
     *
     * @param string $path
     * @throws \InvalidArgumentException
     */
    private function validate_path( string $path ): void {
        // Check for null bytes
        if ( strpos( $path, "\0" ) !== false ) {
            throw new \InvalidArgumentException( 'Path contains null bytes' );
        }

        // Check for directory traversal attempts
        if ( strpos( $path, '..' ) !== false ) {
            throw new \InvalidArgumentException( 'Path contains directory traversal' );
        }

        // Ensure path is within allowed directory
        $allowed_path = PLUGIN_COMPOSER_DIR . '/';
        $real_path = realpath( $path );
        
        if ( $real_path && strpos( $real_path, $allowed_path ) !== 0 ) {
            throw new \InvalidArgumentException( 'Path is outside allowed directory' );
        }
    }
}
