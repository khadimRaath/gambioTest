<?php
/* --------------------------------------------------------------
   UrlRewriteStorage.inc.php 2016-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UrlRewriteStorage
 *
 * @category   System
 * @package    Shared
 * @subpackage Storage
 */
class UrlRewriteStorage
{
	/**
	 * DB Connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * @var NonEmptyStringType
	 */
	protected $contentType;
	
	/**
	 * @var LanguageProviderInterface
	 */
	protected $languageProvider;
	
	/**
	 * @var array
	 */
	protected $validContentTypes = array('product', 'category', 'content', 'search');
	
	
	/**
	 * UrlRewriteStorage constructor.
	 *
	 * @param NonEmptyStringType        $contentType
	 * @param CI_DB_query_builder       $db
	 * @param LanguageProviderInterface $languageProvider
	 *
	 * @throws InvalidArgumentException If an unsupported content type is given.
	 */
	public function __construct(NonEmptyStringType $contentType,
	                            CI_DB_query_builder $db,
	                            LanguageProviderInterface $languageProvider)
	{
		if(!in_array($contentType->asString(), $this->validContentTypes))
		{
			throw new InvalidArgumentException('Invalid content type given. Supported content types are: "product", "category", "content", "search". Got '
			                                   . gettype($contentType) . '): ' . $contentType->asString());
		}
		$this->contentType      = $contentType;
		$this->db               = $db;
		$this->languageProvider = $languageProvider;
	}
	
	
	/**
	 * Returns an UrlRewriteCollection with UrlRewrite instances for the provided content ID.
	 *
	 * @param IdType $contentId
	 *
	 * @return UrlRewriteCollection
	 */
	public function get(IdType $contentId)
	{
		$urlRewrites = array();
		
		$result = $this->db->order_by('language_id')->get_where('url_rewrites', array(
			'content_type' => $this->contentType->asString(),
			'content_id'   => $contentId->asInt()
		))->result_array();
		
		if(count($result))
		{
			foreach($result as $row)
			{
				$languageCode               = $this->languageProvider->getCodeById(new IdType($row['language_id']))
				                                                     ->asString();
				$urlRewrites[$languageCode] = $this->_createUrlRewriteByArray($row);
			}
		}
		
		$urlRewriteCollection = MainFactory::create('UrlRewriteCollection', $urlRewrites);
		
		return $urlRewriteCollection;
	}
	
	
	/**
	 * Returns an UrlRewriteCollection with UrlRewrite instances for the provided rewrite url.
	 *
	 * @param NonEmptyStringType $rewriteUrl
	 *
	 * @return UrlRewriteCollection
	 */
	public function findByRewriteUrl(NonEmptyStringType $rewriteUrl)
	{
		$urlRewrites = array();
		
		$result = $this->db->order_by('language_id')->get_where('url_rewrites', array(
			'content_type' => $this->contentType->asString(),
			'rewrite_url'  => $rewriteUrl->asString()
		))->result_array();
		
		if(count($result))
		{
			foreach($result as $row)
			{
				$languageCode               = $this->languageProvider->getCodeById(new IdType($row['language_id']))
				                                                     ->asString();
				$urlRewrites[$languageCode] = $this->_createUrlRewriteByArray($row);
			}
		}
		
		$urlRewriteCollection = MainFactory::create('UrlRewriteCollection', $urlRewrites);
		
		return $urlRewriteCollection;
	}
	
	
	/**
	 * Returns a single UrlRewrite instance for the provided content ID and language ID or NULL if no entry was found.
	 *
	 * @param IdType $contentId
	 * @param IdType $languageId
	 *
	 * @return null|UrlRewrite
	 */
	public function findByContentIdAndLanguageId(IdType $contentId, IdType $languageId)
	{
		$result = $this->db->get_where('url_rewrites', array(
			'content_type' => $this->contentType->asString(),
			'content_id'   => $contentId->asInt(),
			'language_id'  => $languageId->asInt()
		))->row_array();
		
		if(isset($result))
		{
			return $this->_createUrlRewriteByArray($result);
		}
		
		return null;
	}
	
	
	/**
	 * Saves the given UrlRewriteCollection into the database after old entries were deleted by the provided container
	 * ID.
	 *
	 * @param IdType               $contentId
	 * @param UrlRewriteCollection $collection
	 *
	 * @throws  RuntimeException if the given rewrite url already exists for another entity (products, categories or
	 *                           contents).
	 *
	 * @return UrlRewriteStorage Same instance for chained method calls.
	 */
	public function set(IdType $contentId, UrlRewriteCollection $collection)
	{
		$this->db->delete('url_rewrites', array(
			'content_type' => $this->contentType->asString(),
			'content_id'   => $contentId->asInt()
		));
		
		/** @var UrlRewrite $urlRewrite */
		foreach($collection->getArray() as $key => $urlRewrite)
		{
			// check if rewrite url is unique
			$result = $this->db->get_where('url_rewrites', array(
				'rewrite_url'   => $urlRewrite->getRewriteUrl(),
				'content_id !=' => $urlRewrite->getContentId()
			))->row_array();
			
			if(count($result))
			{
				// add content ID to rewrite url making it unique
				$urlRewrite = MainFactory::create('UrlRewrite', new NonEmptyStringType($urlRewrite->getContentType()),
				                                  new IdType($urlRewrite->getContentId()),
				                                  new IdType($urlRewrite->getLanguageId()),
				                                  new NonEmptyStringType($urlRewrite->getRewriteUrl() . '-'
				                                                         . $urlRewrite->getContentId()),
				                                  new NonEmptyStringType($urlRewrite->getTargetUrl()));
				
				$collection->setValue($key, $urlRewrite);
				$this->set($contentId, $collection);
				
				break;
			}
			
			$this->db->insert('url_rewrites', array(
				'content_id'   => $urlRewrite->getContentId(),
				'content_type' => $this->contentType->asString(),
				'language_id'  => $urlRewrite->getLanguageId(),
				'rewrite_url'  => $urlRewrite->getRewriteUrl(),
				'target_url'   => $urlRewrite->getTargetUrl()
			));
		}
		
		return $this;
	}
	
	
	/**
	 * @param IdType $contentId
	 *
	 * @return UrlRewriteStorage Same instance for chained method calls.
	 */
	public function delete(IdType $contentId)
	{
		
		$this->db->delete('url_rewrites', array(
			'content_type' => $this->contentType->asString(),
			'content_id'   => $contentId->asInt()
		));
		
		return $this;
	}
	
	
	/**
	 * @param array $urlRewriteData
	 *
	 * @return UrlRewrite
	 */
	protected function _createUrlRewriteByArray(array $urlRewriteData)
	{
		$contentId  = new IdType($urlRewriteData['content_id']);
		$languageId = new IdType($urlRewriteData['language_id']);
		$rewriteUrl = new NonEmptyStringType($urlRewriteData['rewrite_url']);
		$targetUrl  = new NonEmptyStringType($urlRewriteData['target_url']);
		
		$urlRewrite = MainFactory::create('UrlRewrite', $this->contentType, $contentId, $languageId, $rewriteUrl,
		                                  $targetUrl);
		
		return $urlRewrite;
	}
}