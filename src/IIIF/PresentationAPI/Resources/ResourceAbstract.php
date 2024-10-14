<?php
/*
 *  This file is part of IIIF Manifest Creator.
 *
 *  IIIF Manifest Creator is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  IIIF Manifest Creator is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with IIIF Manifest Creator.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @category IIIF\PresentationAPI
 *  @package  Resources
 *  @author   Harry Shyket <harry.shyket@yale.edu>
 *  @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
*/

namespace IIIF\PresentationAPI\Resources;

use IIIF\PresentationAPI\Links\Related;
use IIIF\PresentationAPI\Links\Rendering;
use IIIF\PresentationAPI\Links\Service;
use IIIF\PresentationAPI\Metadata\Metadata;
use IIIF\PresentationAPI\Parameters\Identifier;
use IIIF\PresentationAPI\Properties\Logo;
use IIIF\PresentationAPI\Properties\Thumbnail;
use IIIF\Utils\Validator;

/**
 * Abstract implementation of a resource
 */
abstract class ResourceAbstract implements ResourceInterface
{

    protected $type;
    protected $defaultcontext = "http://iiif.io/api/presentation/2/context.json";
    protected $viewingdirection;
    protected $navdate;
    protected $contexts = array();
    protected $labels = array();
    protected $viewinghints = array();
    protected $descriptions = array();
    protected $attributions = array();
    protected $licenses = array();
    protected $thumbnails = array();
    protected $logos = array();
    protected $metadata = array();
    protected $seealso = array();
    protected $services = array();
    protected $related = array();
    protected $rendering = array();
    protected $within = array();
    private $id;
    private $onlyid = false;
    private $istoplevel = false;
    private $onlymemberdata = false;

    /**
     * Sets whether the item is a top level item.
     */
    function __construct(bool $top = false)
    {
        $this->istoplevel = (bool)$top;

        if ($this->istoplevel) {
            $this->addContext($this->getDefaultContext());
        }
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addContext()
     */
    public function addContext($context): static
    {
        array_push($this->contexts, $context);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getDefaultContext()
     */
    public function getDefaultContext(): string
    {
        return $this->defaultcontext;
    }

    /**
     * Set just the id to return instead of the class object.
     */
    public function returnOnlyID(): static
    {
        $this->onlyid = true;
        return $this;
    }

    /**
     * Check whether to only return the ID instead of the object.
     */
    public function getOnlyID(): bool
    {
        return $this->onlyid;
    }

    /**
     * Usage when a resource only needs @id, @type and label.
     */
    public function returnOnlyMemberData(): static
    {
        $this->onlymemberdata = true;
        return $this;
    }

    /**
     * Return whether only certain data fields are needed.
     */
    public function getOnlyMemberData(): bool
    {
        return $this->onlymemberdata;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::isTopLevel()
     */
    public function isTopLevel(): bool
    {
        return $this->istoplevel;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getContext()
     */
    public function getContexts(): array
    {
        if (count($this->contexts) == 1) {
            return $this->contexts[0];
        }
        return $this->contexts;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getID()
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     * @param string
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::setID()
     */
    public function setID($id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getType()
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     * @param string
     * @param string
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addLabel()
     */
    public function addLabel($label, $language = NULL): static
    {
        if (!empty($language)) {
            $label = array(Identifier::ATVALUE => $label, Identifier::LANGUAGE => $language);
        }

        array_push($this->labels, $label);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getLabelss()
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * {@inheritDoc}
     * @param string
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addViewingHints()
     */
    public function addViewingHint($viewinghint): static
    {
        // Make sure that the viewing hint is an allowed value
        $allviewinghints = new \ReflectionClass('\IIIF\PresentationAPI\Parameters\ViewingHint');
        if (Validator::inArray($viewinghint, $allviewinghints->getConstants(), "Illegal viewingHint selected")) {
            array_push($this->viewinghints, $viewinghint);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getViewingHints()
     */
    public function getViewingHints(): array
    {
        return $this->viewinghints;
    }

    /**
     * {@inheritDoc}
     * @param string
     * @param string
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addDescription()
     */
    public function addDescription($description, $language = NULL): static
    {
        if (!empty($language)) {
            $description = array(Identifier::ATVALUE => $description, Identifier::LANGUAGE => $language);
        }

        array_push($this->descriptions, $description);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getDescriptions()
     * @return string[]
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /**
     * {@inheritDoc}
     * @param string
     * @param string
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addAttribution()
     */
    public function addAttribution($attribution, $language = NULL): static
    {
        if (!empty($language)) {
            $attribution = array(Identifier::ATVALUE => $attribution, Identifier::LANGUAGE => $language);
        }

        array_push($this->attributions, $attribution);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getAttributions()
     */
    public function getAttributions(): array
    {
        return $this->attributions;
    }

    /**
     * {@inheritDoc}
     * @param string
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addLicense()
     */
    public function addLicense($license): static
    {
        // Make sure it is a valid URL
        if (Validator::validateURL($license, "The license must be a valid URL")) {
            array_push($this->licenses, $license);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getLicenses()
     */
    public function getLicenses(): array
    {
        return $this->licenses;
    }

    /**
     * {@inheritDoc}
     * @param \IIIF\PresentationAPI\Properties\Thumbnail
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addThumbnail()
     */
    public function addThumbnail(Thumbnail $thumbnail): static
    {
        array_push($this->thumbnails, $thumbnail);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getThumbnails()
     */
    public function getThumbnails(): array
    {
        return $this->thumbnails;
    }

    /**
     * {@inheritDoc}
     * @param \IIIF\PresentationAPI\Properties\Logo
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addLogo()
     */
    public function addLogo(Logo $logo): static
    {
        array_push($this->logos, $logo);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getLogos()
     */
    public function getLogos(): array
    {
        return $this->logos;
    }

    /**
     * Get the metadata
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Set the metadata
     * @param \IIIF\PresentationAPI\Metadata\Metadata $metadata
     */
    public function setMetadata(Metadata $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @param \IIIF\PresentationAPI\Links\SeeAlso $seealso
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addSeeAlso()
     */
    public function addSeeAlso($seealso): static
    {
        array_push($this->seealso, $seealso);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getAttributions()
     */
    public function getSeeAlso(): array
    {
        return $this->seealso;
    }

    /**
     * Get the navDate
     */
    public function getNavDate()
    {
        return $this->navdate;
    }

    /**
     * Set the navDate.
     * @param Date $navdate
     */
    public function setNavDate($navdate): static
    {
        date_default_timezone_set("UTC");
        $time = strtotime($navdate);

        if ($time) {
            $this->navdate = date("Y-d-m\TH:i:s\Z", strtotime($navdate));
        } else {
            $this->navdate = "00:00:00";
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * @param \IIIF\PresentationAPI\Links\Service
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addService()
     */
    public function addService(Service $service): static
    {
        array_push($this->services, $service);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return array
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getAttributions()
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addRelated()
     */
    public function addRelated(Related $related): static
    {
        array_push($this->related, $related);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getRelated()
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addRendering()
     */
    public function addRendering(Rendering $rendering): static
    {
        array_push($this->rendering, $rendering);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getRendering()
     */
    public function getRendering()
    {
        return $this->rendering;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::addWithin()
     */
    public function addWithin($within): static
    {
        array_push($this->within, $within);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::getWithin()
     */
    public function getWithin()
    {
        return $this->within;
    }

    /**
     * Get the viewing direction.
     */
    public function getViewingDirection(): string
    {
        return $this->viewingdirection;
    }

    /**
     * Set the viewing direction.
     *
     * @param string $viewingdirection
     */
    public function setViewingDirection($viewingdirection): static
    {
        // Make sure that the viewing hint is an allowed value
        $allviewingdirections = new \ReflectionClass('\IIIF\PresentationAPI\Parameters\ViewingDirection');
        if (Validator::inArray($viewingdirection, $allviewingdirections->getConstants(), "Illegal viewingDirection selected")) {
            $this->viewingdirection = $viewingdirection;
        }
        return $this;
    }

    /**
     * Create an array from the class elements.
     * {@inheritDoc}
     * @see \IIIF\PresentationAPI\Resources\ResourceInterface::toArray()
     */
    abstract public function toArray();

}
