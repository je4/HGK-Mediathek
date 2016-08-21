<?php
/**
 * This file is part of MediathekMiner.
 * MediathekMiner is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * Foobar is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with Foobar. If not, see http://www.gnu.org/licenses/.
 *
 *
 * @package     MediathekMiner
 * @subpackage  autoload
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 * 
 */

/**
 * @namespace
 */

namespace Passbook;

/**
 * Autoloader.
 *
 * This class is included to allow for easy usage of Mediathek.

 */
class Autoloader
{
    /**
     * Register the Mediathek autoloader.
     *
     * The autoloader only acts for classnames that start with 'Mediathek'. It
     * will be appended to any other autoloaders already registered.
     *
     * @static
     */
    public static function register()
    {
        spl_autoload_register(array(new self(), 'load'));
    }

    /**
     * Autoload a class.
     *
     * This method is automatically called after registering this autoloader.
     * The autoloader only acts for classnames that start with 'Mediathek'.
     *
     * @static
     *
     * @param string $class
     */
    public static function load($class)
    {
        if (substr($class, 0, 8) == __NAMESPACE__) {
            $class = str_replace(
                array(__NAMESPACE__, '\\'),
                array('', '/'),
                $class
            );

            $file = dirname(__FILE__).$class.'.php';
//            echo $file ."\n";
            require $file;
        }
    }
}
