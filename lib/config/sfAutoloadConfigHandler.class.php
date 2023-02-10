<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id$
 */
class sfAutoloadConfigHandler extends sfYamlConfigHandler
{
  /**
   * Executes this configuration handler.
   *
   * @param array $configFiles An array of absolute filesystem path to a configuration file
   *
   * @return string Data to be written to a cache file
   *
   * @throws sfConfigurationException If a requested configuration file does not exist or is not readable
   * @throws sfParseException If a requested configuration file is improperly formatted
   */
  public function execute($configFiles)
  {
    // set our required categories list and initialize our handler
    $this->initialize(array('required_categories' => array('autoload')));

    $data = array();
    foreach ($this->parse($configFiles) as $name => $mapping)
    {
      $data[] = sprintf("\n  // %s", $name);

      foreach ($mapping as $class => $file)
      {
        $data[] = sprintf("  '%s' => '%s',", $class, str_replace('\\', '\\\\', $file));
      }
    }

    // compile data
    return sprintf("<?php\n".
                      "// auto-generated by sfAutoloadConfigHandler\n".
                      "// date: %s\nreturn array(\n%s\n);\n",
                      date('Y/m/d H:i:s'), implode("\n", $data));
  }

  public function evaluate($configFiles)
  {
    $mappings = array();
    foreach ($this->parse($configFiles) as $mapping)
    {
      foreach ($mapping as $class => $file)
      {
        $mappings[$class] = $file;
      }
    }

    return $mappings;
  }

  protected function parse(array $configFiles)
  {
    // parse the yaml
    $config = static::getConfiguration($configFiles);

    $mappings = array();
    foreach ($config['autoload'] as $name => $entry)
    {
      $mapping = array();

      // file mapping or directory mapping?
      if (isset($entry['files']))
      {
        // file mapping
        foreach ($entry['files'] as $class => $file)
        {
          $mapping[strtolower((string) $class)] = $file;
        }
      }
      else
      {
        // directory mapping
        $ext  = isset($entry['ext']) ? $entry['ext'] : '.php';
        $path = $entry['path'];

        // we automatically add our php classes
        require_once(sfConfig::get('sf_symfony_lib_dir').'/util/sfFinder.class.php');
        $finder = sfFinder::type('file')->name('*'.$ext)->follow_link();

        // recursive mapping?
        $recursive = isset($entry['recursive']) ? $entry['recursive'] : false;
        if (!$recursive)
        {
          $finder->maxdepth(0);
        }

        // exclude files or directories?
        if (isset($entry['exclude']) && is_array($entry['exclude']))
        {
          $finder->prune($entry['exclude'])->discard($entry['exclude']);
        }

        if ($matches = glob($path))
        {
          foreach ($finder->in($matches) as $file)
          {
            $mapping = array_merge($mapping, $this->parseFile($path, $file, isset($entry['prefix']) ? $entry['prefix'] : ''));
          }
        }
      }

      $mappings[$name] = $mapping;
    }

    return $mappings;
  }

  static public function parseFile($path, $file, $prefix)
  {
    $mapping = array();
    preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface|trait)\s+(\w+)~mi', file_get_contents($file), $classes);
    foreach ($classes[1] as $class)
    {
      $localPrefix = '';
      if ($prefix)
      {
        // FIXME: does not work for plugins installed with a symlink
        preg_match('~^'.str_replace('\*', '(.+?)', preg_quote(str_replace('/', DIRECTORY_SEPARATOR, $path), '~')).'~', str_replace('/', DIRECTORY_SEPARATOR, $file), $match);
        if (isset($match[$prefix]))
        {
          $localPrefix = $match[$prefix].'/';
        }
      }

      $mapping[$localPrefix.strtolower((string) $class)] = $file;
    }

    return $mapping;
  }

  /**
   * @see sfConfigHandler
   * @inheritdoc
   */
  static public function getConfiguration(array $configFiles)
  {
    $configuration = sfProjectConfiguration::getActive();

    $pluginPaths = $configuration->getPluginPaths();
    $pluginConfigFiles = array();

    // move plugin files to front
    foreach ($configFiles as $i => $configFile)
    {
      $configFilePath = str_replace(DIRECTORY_SEPARATOR, '/', $configFile);
      $path = str_replace(DIRECTORY_SEPARATOR, '/', realpath(implode('/', array_slice(explode('/', $configFilePath), 0, -2))));
      if (in_array($path, $pluginPaths))
      {
        $pluginConfigFiles[] = $configFile;
        unset($configFiles[$i]);
      }
    }

    $configFiles = array_merge($pluginConfigFiles, $configFiles);

    $config = static::replaceConstants(static::parseYamls($configFiles));

    foreach ($config['autoload'] as $name => $values)
    {
      if (isset($values['path']))
      {
        $config['autoload'][$name]['path'] = static::replacePath($values['path']);
      }
    }

    $event = $configuration->getEventDispatcher()->filter(new sfEvent(__CLASS__, 'autoload.filter_config'), $config);
    $config = $event->getReturnValue();

    return $config;
  }
}
