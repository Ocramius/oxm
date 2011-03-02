<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\OXM;

use \Doctrine\Common\Cache\Cache,
    \Doctrine\OXM\Marshaller\Marshaller,
    \Doctrine\OXM\Mapping\Driver\Driver;

/**
 * Configuration container for all configuration options of Doctrine.
 * It combines all configuration options from DBAL & ORM.
 */
class Configuration
{
    /**
     * The attributes that are contained in the configuration.
     * Values are default values.
     *
     * @var array
     */
    protected $_attributes = array();

    /**
     * Sets the cache driver implementation that is used for metadata caching.
     *
     * @param Driver $driverImpl
     * @todo Force parameter to be a Closure to ensure lazy evaluation
     *       (as soon as a metadata cache is in effect, the driver never needs to initialize).
     */
    public function setClassMetadataDriverImpl(Driver $driverImpl)
    {
        $this->_attributes['classMetadataDriverImpl'] = $driverImpl;
    }

    /**
     * Gets the cache driver implementation that is used for the mapping metadata.
     *
     * @throws ORMException
     * @return Mapping\Driver\Driver
     */
    public function getClassMetadataDriverImpl()
    {
        return isset($this->_attributes['classMetadataDriverImpl']) ?
                $this->_attributes['classMetadataDriverImpl'] : null;
    }



    /**
     * Gets the cache driver implementation that is used for metadata caching.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getClassMetadataCacheImpl()
    {
        return isset($this->_attributes['classMetadataCacheImpl']) ?
                $this->_attributes['classMetadataCacheImpl'] : null;
    }

    /**
     * Sets the cache driver implementation that is used for metadata caching.
     *
     * @param \Doctrine\Common\Cache\Cache $cacheImpl
     */
    public function setClassMetadataCacheImpl(Cache $cacheImpl)
    {
        $this->_attributes['classMetadataCacheImpl'] = $cacheImpl;
    }

    /**
     * @return \Doctrine\OXM\Marshaller\Marshaller
     */
    public function getMarshallerClassName()
    {
        if (!isset($this->_attributes['marshallerclassName'])) {
            // todo - put most efficient marshaller here
            $this->_attributes['marshallerclassName'] = 'Doctrine\OXM\Marshaller\SimpleXmlMarshaller';
        }
        return $this->_attributes['marshallerclassName'];
    }

    /**
     * @param string $marshallerClassName
     * @return void
     */
    public function setMarshallerClassName($marshallerClassName)
    {
        $this->_attributes['marshallerclassName'] = $marshallerClassName;
    }

    /**
     *
     */
    public function setStoragePath($path)
    {
        $this->_attributes['storagePath'] = $path;
    }

    /**
     * 
     */
    public function getStoragePath()
    {
        return isset($this->_attributes['storagePath']) ?
                $this->_attributes['storagePath'] : null;
    }


    /**
     * Add a new default annotation driver with a correctly configured annotation reader.
     * 
     * @param array $paths
     * @return Mapping\Driver\AnnotationDriver
     */
    public function newDefaultAnnotationDriver($paths = array())
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reader->setDefaultAnnotationNamespace('Doctrine\OXM\Mapping\\');
        
        return new \Doctrine\OXM\Mapping\Driver\AnnotationDriver($reader, (array)$paths);
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * @param string $entityNamespaceAlias
     * @return string
     * @throws MappingException
     */
    public function getEntityNamespace($entityNamespaceAlias)
    {
        if ( ! isset($this->_attributes['entityNamespaces'][$entityNamespaceAlias])) {
            throw OXMException::unknownEntityNamespace($entityNamespaceAlias);
        }

        return trim($this->_attributes['entityNamespaces'][$entityNamespaceAlias], '\\');
    }

    /**
     * Set the entity alias map
     *
     * @param array $entityAliasMap
     * @return void
     */
    public function setEntityNamespaces(array $entityNamespaces)
    {
        $this->_attributes['entityNamespaces'] = $entityNamespaces;
    }



    /**
     * Ensures that this Configuration instance contains settings that are
     * suitable for a production environment.
     *
     * @throws ORMException If a configuration setting has a value that is not
     *                      suitable for a production environment.
     */
    public function ensureProductionSettings()
    {
        if ( !$this->getClassMetadataCacheImpl()) {
            throw OXMException::mappingCacheNotConfigured();
        }
    }

    /**
     * Set a class metadata factory.
     * 
     * @param string $cmf
     */
    public function setMappingFactoryName($cmfName)
    {
        $this->_attributes['classMetadataFactoryName'] = $cmfName;
    }

    /**
     * @return string
     */
    public function getClassMetadataFactoryName()
    {
        if (!isset($this->_attributes['classMetadataFactoryName'])) {
            $this->_attributes['classMetadataFactoryName'] = 'Doctrine\OXM\Mapping\ClassMetadataFactory';
        }
        return $this->_attributes['classMetadataFactoryName'];
    }
}