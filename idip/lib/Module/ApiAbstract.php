<?php
abstract class Module_ApiAbstract extends ModuleAbstract
{
	/**
	 * Returns an associative array containing all available request parameters
	 *
	 * @return array
	 */
	protected function getRequestParams()
	{
		$params = array();
		
		return $params;
	}

	/**
	 * Returns an PSX_Data_ReaderResult object depending of the $reader
	 * string. If the reader type is not set the content-type of the request is
	 * used to get the best fitting reader. You can use the method import of
	 * an record to transform the request into an record
	 *
	 * @param integer $readerType
	 * @return PSX_Data_ReaderResult
	 */
	protected function getRequest($readerType = null)
	{
		// find best reader type
		if($readerType === null)
		{
			$contentType = Base::getRequestHeader('Content-Type');
			$readerType  = Data_ReaderFactory::getReaderTypeByContentType($contentType);
		}

		// get reader
		$reader = Data_ReaderFactory::getReader($readerType);

		if($reader === null)
		{
			throw new Exception('Could not find fitting data reader');
		}

		// try to read request
		$request = $this->base->getRequest();

		return $reader->read($request);
	}

	/**
	 * Writes the $record with the writer $writerType or depending on the
	 * get parameter format or of the mime type of the Accept header.
	 *
	 * @param PSX_Data_Record $record
	 * @param integer $writerType
	 * @param integer $code
	 * @return void
	 */
	protected function setResponse(Data_RecordInterface $record, $writerType = null, $code = 200)
	{
		// set response code
		PSX_Base::setResponseCode($code);

		// find best writer type if not set
		if($writerType === null)
		{
			$formats = array(

				'atom' => Data_Writer_Atom::$mime,
				'form' => Data_Writer_Form::$mime,
				'json' => Data_Writer_Json::$mime,
				'rss'  => Data_Writer_Rss::$mime,
				'xml'  => Data_Writer_Xml::$mime,

			);

			$format      = isset($_GET['format']) && strlen($_GET['format']) < 16 ? $_GET['format'] : null;
			$contentType = isset($formats[$format]) ? $formats[$format] : Base::getRequestHeader('Accept');
			$writerType  = Data_WriterFactory::getWriterTypeByContentType($contentType);
		}

		// get writer
		$writer = Data_WriterFactory::getWriter($writerType);

		if($writer === null)
		{
			throw new Exception('Could not find fitting data writer');
		}

		// try to write response with preferred writer
		$writerResult = new Data_WriterResult($writerType, $writer);

		$this->setWriterConfig($writerResult);

		$writer->write($record);
	}

	/**
	 * You can override this method to configure the writer. Some writers
	 * require configuration i.e. the atom writer needs to know wich fields
	 * should be used for an entry.
	 *
	 * @param PSX_Data_WriterResult $result
	 * @return void
	 */
	protected function setWriterConfig(Data_WriterResult $result)
	{
	}
}

