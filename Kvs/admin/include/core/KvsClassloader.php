<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Classloader for KVS classes.
 */
final class KvsClassloader
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	/**
	 * @var array
	 */
	private static $CLASSES;

	/**
	 * @var array
	 */
	private static $CATEGORIZATION_CLASSES;

	/**
	 * @var array
	 */
	private static $CONTENT_CLASSES;

	/**
	 * Loads class of the given name.
	 *
	 * @param string $classname
	 */
	public static function load_class(string $classname): void
	{
		global $config;

		if (!isset($config['project_path']))
		{
			throw new RuntimeException('Attempt to use classloader without project_path set');
		}

		self::init_classes();

		$class_path = self::$CLASSES[$classname] ?? null;
		if ($class_path && is_file($class_path))
		{
			require_once $class_path;
		}
	}

	/**
	 * Registers new class.
	 *
	 * @param string $classname
	 * @param string $path
	 */
	public static function register_class(string $classname, string $path): void
	{
		self::init_classes();
		if (!isset(self::$CLASSES[$classname]))
		{
			self::$CLASSES[$classname] = $path;
		}
	}

	/**
	 * Lists all object type classes.
	 *
	 * @return KvsAbstractPersistentObjectType[]
	 */
	public static function list_all_object_types(): array
	{
		global $config;

		self::init_classes();

		$core_path = "$config[project_path]/admin/include/core";
		foreach (self::$CLASSES as $class_path)
		{
			if (strpos($class_path, "$core_path/domain/type/") === 0 || strpos($class_path, "ObjectType") !== false)
			{
				require_once $class_path;
			}
		}

		$result = [];
		try
		{
			foreach (get_declared_classes() as $class)
			{
				$reflection_class = new ReflectionClass($class);
				if (!$reflection_class->isAbstract() && $reflection_class->isSubclassOf(KvsAbstractPersistentObjectType::class))
				{
					$result[] = $class::get_instance();
				}
			}
		} catch (ReflectionException $e)
		{
			throw new RuntimeException('Failed to populate object types list', 0, $e);
		}
		return $result;
	}

	/**
	 * Lists all categorization object type classes.
	 *
	 * @return KvsAbstractPersistentObjectType[]
	 */
	public static function list_all_categorization_object_types(): array
	{
		self::init_classes();

		foreach (self::$CATEGORIZATION_CLASSES as $class_name)
		{
			if (isset(self::$CLASSES[$class_name]))
			{
				require_once self::$CLASSES[$class_name];
			}
		}

		$result = [];
		try
		{
			foreach (get_declared_classes() as $class)
			{
				$reflection_class = new ReflectionClass($class);
				if (!$reflection_class->isAbstract() && $reflection_class->isSubclassOf(KvsAbstractCategorizationType::class))
				{
					$result[] = $class::get_instance();
				}
			}
		} catch (ReflectionException $e)
		{
			throw new RuntimeException('Failed to populate categorization object types list', 0, $e);
		}
		return $result;
	}

	/**
	 * Lists all content object type classes.
	 *
	 * @return KvsAbstractPersistentObjectType[]
	 */
	public static function list_all_content_object_types(): array
	{
		self::init_classes();

		foreach (self::$CONTENT_CLASSES as $class_name)
		{
			if (isset(self::$CLASSES[$class_name]))
			{
				require_once self::$CLASSES[$class_name];
			}
		}

		$result = [];
		try
		{
			foreach (get_declared_classes() as $class)
			{
				$reflection_class = new ReflectionClass($class);
				if (!$reflection_class->isAbstract() && $reflection_class->isSubclassOf(KvsAbstractContentType::class))
				{
					$result[] = $class::get_instance();
				}
			}
		} catch (ReflectionException $e)
		{
			throw new RuntimeException('Failed to populate content object types list', 0, $e);
		}
		return $result;
	}

	/**
	 * Lists all event listener classes.
	 *
	 * @return KvsEventListener[]
	 */
	public static function list_all_event_listeners(): array
	{
		self::init_classes();

		foreach (self::$CLASSES as $class_path)
		{
			if (strpos($class_path, "EventListener") !== false)
			{
				require_once $class_path;
			}
		}

		$result = [];
		try
		{
			foreach (get_declared_classes() as $class)
			{
				$reflection_class = new ReflectionClass($class);
				if (!$reflection_class->isAbstract() && $reflection_class->implementsInterface('KvsEventListener'))
				{
					$result[] = $reflection_class->newInstance();
				}
			}
		} catch (ReflectionException $e)
		{
			throw new RuntimeException('Failed to populate global event listeners list', 0, $e);
		}
		return $result;
	}

	/**
	 * Populates list of supported classes.
	 */
	private static function init_classes(): void
	{
		global $config;

		if (!self::$CLASSES)
		{
			self::$CLASSES = [];

			$core_path = "$config[project_path]/admin/include/core";

			// core
			self::$CLASSES['KvsContext'] = "$core_path/KvsContext.php";
			self::$CLASSES['KvsException'] = "$core_path/KvsException.php";
			self::$CLASSES['KvsFilesystem'] = "$core_path/KvsFilesystem.php";
			self::$CLASSES['KvsImagemagick'] = "$core_path/KvsImagemagick.php";
			self::$CLASSES['KvsInterruptionException'] = "$core_path/KvsInterruptionException.php";
			self::$CLASSES['KvsNetwork'] = "$core_path/KvsNetwork.php";
			self::$CLASSES['KvsSecurityException'] = "$core_path/KvsSecurityException.php";
			self::$CLASSES['KvsUtilities'] = "$core_path/KvsUtilities.php";

			// common
			self::$CLASSES['KvsAbstractRemoteAPIExecutor'] = "$core_path/common/KvsAbstractRemoteAPIExecutor.php";
			self::$CLASSES['KvsEventListener'] = "$core_path/common/KvsEventListener.php";
			self::$CLASSES['KvsEventQueue'] = "$core_path/common/KvsEventQueue.php";

			// core domain
			self::$CLASSES['KvsAbstractAdministrativeType'] = "$core_path/domain/KvsAbstractAdministrativeType.php";
			self::$CLASSES['KvsAbstractCategorizationType'] = "$core_path/domain/KvsAbstractCategorizationType.php";
			self::$CLASSES['KvsAbstractContentType'] = "$core_path/domain/KvsAbstractContentType.php";
			self::$CLASSES['KvsAbstractDataField'] = "$core_path/domain/KvsAbstractDataField.php";
			self::$CLASSES['KvsAbstractDataType'] = "$core_path/domain/KvsAbstractDataType.php";
			self::$CLASSES['KvsAbstractEnum'] = "$core_path/domain/KvsAbstractEnum.php";
			self::$CLASSES['KvsAbstractPersistentDataType'] = "$core_path/domain/KvsAbstractPersistentDataType.php";
			self::$CLASSES['KvsAbstractPersistentObjectType'] = "$core_path/domain/KvsAbstractPersistentObjectType.php";
			self::$CLASSES['KvsAdminObject'] = "$core_path/domain/KvsAdminObject.php";
			self::$CLASSES['KvsCalculatableField'] = "$core_path/domain/KvsCalculatableField.php";
			self::$CLASSES['KvsDataReference'] = "$core_path/domain/KvsDataReference.php";
			self::$CLASSES['KvsDisplayableData'] = "$core_path/domain/KvsDisplayableData.php";
			self::$CLASSES['KvsObjectStatusEnum'] = "$core_path/domain/KvsObjectStatusEnum.php";
			self::$CLASSES['KvsPersistentData'] = "$core_path/domain/KvsPersistentData.php";
			self::$CLASSES['KvsPersistentField'] = "$core_path/domain/KvsPersistentField.php";
			self::$CLASSES['KvsPersistentObject'] = "$core_path/domain/KvsPersistentObject.php";
			self::$CLASSES['KvsPersistentRelationship'] = "$core_path/domain/KvsPersistentRelationship.php";
			self::$CLASSES['KvsReferenceField'] = "$core_path/domain/KvsReferenceField.php";
			self::$CLASSES['KvsUserObject'] = "$core_path/domain/KvsUserObject.php";
			self::$CLASSES['KvsVirtualField'] = "$core_path/domain/KvsVirtualField.php";

			// data policies
			self::$CLASSES['KvsAbstractDataPolicy'] = "$core_path/domain/policy/KvsAbstractDataPolicy.php";
			self::$CLASSES['KvsDataPolicyAfterDelete'] = "$core_path/domain/policy/KvsDataPolicyAfterDelete.php";
			self::$CLASSES['KvsDataPolicyAfterSave'] = "$core_path/domain/policy/KvsDataPolicyAfterSave.php";
			self::$CLASSES['KvsDataPolicyAuditLogging'] = "$core_path/domain/policy/KvsDataPolicyAuditLogging.php";
			self::$CLASSES['KvsDataPolicyBeforeDelete'] = "$core_path/domain/policy/KvsDataPolicyBeforeDelete.php";
			self::$CLASSES['KvsDataPolicyBeforeSave'] = "$core_path/domain/policy/KvsDataPolicyBeforeSave.php";
			self::$CLASSES['KvsDataPolicyDefaultPublicQuery'] = "$core_path/domain/policy/KvsDataPolicyDefaultPublicQuery.php";
			self::$CLASSES['KvsDataPolicyDefaultValue'] = "$core_path/domain/policy/KvsDataPolicyDefaultValue.php";
			self::$CLASSES['KvsDataPolicyFieldType'] = "$core_path/domain/policy/KvsDataPolicyFieldType.php";
			self::$CLASSES['KvsDataPolicyGenerateDirectory'] = "$core_path/domain/policy/KvsDataPolicyGenerateDirectory.php";
			self::$CLASSES['KvsDataPolicyOnPublicQuery'] = "$core_path/domain/policy/KvsDataPolicyOnPublicQuery.php";
			self::$CLASSES['KvsDataPolicyOnValidate'] = "$core_path/domain/policy/KvsDataPolicyOnValidate.php";
			self::$CLASSES['KvsDataPolicyPublicQueryAdvancedFiltering'] = "$core_path/domain/policy/KvsDataPolicyPublicQueryAdvancedFiltering.php";
			self::$CLASSES['KvsDataValidationErrors'] = "$core_path/domain/policy/KvsDataValidationErrors.php";
			self::$CLASSES['KvsDataValidationException'] = "$core_path/domain/policy/KvsDataValidationException.php";

			// object types
			self::$CLASSES['KvsDataTypeAdminNotification'] = "$core_path/domain/type/KvsDataTypeAdminNotification.php";
			self::$CLASSES['KvsDataTypeAdminSetting'] = "$core_path/domain/type/KvsDataTypeAdminSetting.php";
			self::$CLASSES['KvsDataTypeFileHistory'] = "$core_path/domain/type/KvsDataTypeFileHistory.php";
			self::$CLASSES['KvsDataTypeLogAudit'] = "$core_path/domain/type/KvsDataTypeLogAudit.php";
			self::$CLASSES['KvsDataTypeRatingHistory'] = "$core_path/domain/type/KvsDataTypeRatingHistory.php";
			self::$CLASSES['KvsDataTypeUserSubscription'] = "$core_path/domain/type/KvsDataTypeUserSubscription.php";
			self::$CLASSES['KvsObjectTypeAdmin'] = "$core_path/domain/type/KvsObjectTypeAdmin.php";
			self::$CLASSES['KvsObjectTypeAdminGroup'] = "$core_path/domain/type/KvsObjectTypeAdminGroup.php";
			self::$CLASSES['KvsObjectTypeAlbum'] = "$core_path/domain/type/KvsObjectTypeAlbum.php";
			self::$CLASSES['KvsObjectTypeCategory'] = "$core_path/domain/type/KvsObjectTypeCategory.php";
			self::$CLASSES['KvsObjectTypeCategoryGroup'] = "$core_path/domain/type/KvsObjectTypeCategoryGroup.php";
			self::$CLASSES['KvsObjectTypeContentSource'] = "$core_path/domain/type/KvsObjectTypeContentSource.php";
			self::$CLASSES['KvsObjectTypeContentSourceGroup'] = "$core_path/domain/type/KvsObjectTypeContentSourceGroup.php";
			self::$CLASSES['KvsObjectTypeCountry'] = "$core_path/domain/type/KvsObjectTypeCountry.php";
			self::$CLASSES['KvsObjectTypeDvd'] = "$core_path/domain/type/KvsObjectTypeDvd.php";
			self::$CLASSES['KvsObjectTypeDvdGroup'] = "$core_path/domain/type/KvsObjectTypeDvdGroup.php";
			self::$CLASSES['KvsObjectTypeFeedback'] = "$core_path/domain/type/KvsObjectTypeFeedback.php";
			self::$CLASSES['KvsObjectTypeLanguage'] = "$core_path/domain/type/KvsObjectTypeLanguage.php";
			self::$CLASSES['KvsObjectTypeLookup'] = "$core_path/domain/type/KvsObjectTypeLookup.php";
			self::$CLASSES['KvsObjectTypeModel'] = "$core_path/domain/type/KvsObjectTypeModel.php";
			self::$CLASSES['KvsObjectTypeModelGroup'] = "$core_path/domain/type/KvsObjectTypeModelGroup.php";
			self::$CLASSES['KvsObjectTypePlaylist'] = "$core_path/domain/type/KvsObjectTypePlaylist.php";
			self::$CLASSES['KvsObjectTypePost'] = "$core_path/domain/type/KvsObjectTypePost.php";
			self::$CLASSES['KvsObjectTypePostType'] = "$core_path/domain/type/KvsObjectTypePostType.php";
			self::$CLASSES['KvsObjectTypeReferer'] = "$core_path/domain/type/KvsObjectTypeReferer.php";
			self::$CLASSES['KvsObjectTypeTag'] = "$core_path/domain/type/KvsObjectTypeTag.php";
			self::$CLASSES['KvsObjectTypeUser'] = "$core_path/domain/type/KvsObjectTypeUser.php";
			self::$CLASSES['KvsObjectTypeVideo'] = "$core_path/domain/type/KvsObjectTypeVideo.php";

			// site engine functionality
			self::$CLASSES['KvsAbstractDataSiteBlock'] = "$core_path/site/KvsAbstractDataSiteBlock.php";
			self::$CLASSES['KvsAbstractSiteBlock'] = "$core_path/site/KvsAbstractSiteBlock.php";
			self::$CLASSES['KvsListDataSiteBlock'] = "$core_path/site/KvsListDataSiteBlock.php";
			self::$CLASSES['KvsSiteBlockParameter'] = "$core_path/site/KvsSiteBlockParameter.php";
			self::$CLASSES['KvsSiteBlockParameterOption'] = "$core_path/site/KvsSiteBlockParameterOption.php";
			self::$CLASSES['KvsSiteBlockStatusException'] = "$core_path/site/KvsSiteBlockStatusException.php";
			self::$CLASSES['KvsSiteErrors'] = "$core_path/site/KvsSiteErrors.php";
			self::$CLASSES['KvsViewDataSiteBlock'] = "$core_path/site/KvsViewDataSiteBlock.php";

			// admin panel functionality
			self::$CLASSES['KvsAbstractAdminBasicController'] = "$core_path/ap/KvsAbstractAdminBasicController.php";
			self::$CLASSES['KvsAbstractAdminController'] = "$core_path/ap/KvsAbstractAdminController.php";
			self::$CLASSES['KvsAbstractAdminDisplayController'] = "$core_path/ap/KvsAbstractAdminDisplayController.php";
			self::$CLASSES['KvsAbstractAdminModule'] = "$core_path/ap/KvsAbstractAdminModule.php";
			self::$CLASSES['KvsAdminPanel'] = "$core_path/ap/KvsAdminPanel.php";
			self::$CLASSES['KvsAdminBasicAddController'] = "$core_path/ap/KvsAdminBasicAddController.php";
			self::$CLASSES['KvsAdminBasicEditController'] = "$core_path/ap/KvsAdminBasicEditController.php";
			self::$CLASSES['KvsAdminBasicListController'] = "$core_path/ap/KvsAdminBasicListController.php";
			self::$CLASSES['KvsCategorizationAdminModule'] = "$core_path/ap/KvsCategorizationAdminModule.php";
			self::$CLASSES['KvsSettingsAdminModule'] = "$core_path/ap/KvsSettingsAdminModule.php";
			self::$CLASSES['KvsSiteBlockDocumentator'] = "$core_path/ap/KvsSiteBlockDocumentator.php";
			self::$CLASSES['KvsAdminNotificationEmailingEventListener'] = "$core_path/ap/notification/KvsAdminNotificationEmailingEventListener.php";
			self::$CLASSES['KvsAdminNotificationEnum'] = "$core_path/ap/notification/KvsAdminNotificationEnum.php";
			self::$CLASSES['KvsAdminNotificationUpdateEventListener'] = "$core_path/ap/notification/KvsAdminNotificationUpdateEventListener.php";
			self::$CLASSES['KvsAdminInsightController'] = "$core_path/ap/insight/KvsAdminInsightController.php";
			self::$CLASSES['KvsAdminInsightDataProvider'] = "$core_path/ap/insight/KvsAdminInsightDataProvider.php";
			self::$CLASSES['KvsAdminInsightObjectDataProvider'] = "$core_path/ap/insight/KvsAdminInsightObjectDataProvider.php";
			self::$CLASSES['KvsAdminInsightCountryDataProvider'] = "$core_path/ap/insight/KvsAdminInsightCountryDataProvider.php";

			// sql processing
			self::$CLASSES['KvsAbstractArrayQueryExecutor'] = "$core_path/sql/KvsAbstractArrayQueryExecutor.php";
			self::$CLASSES['KvsAbstractQueryExecutor'] = "$core_path/sql/KvsAbstractQueryExecutor.php";
			self::$CLASSES['KvsInternalQueryExecutor'] = "$core_path/sql/KvsInternalQueryExecutor.php";
			self::$CLASSES['KvsPublicQueryExecutor'] = "$core_path/sql/KvsPublicQueryExecutor.php";
			self::$CLASSES['KvsProtectedQueryExecutor'] = "$core_path/sql/KvsProtectedQueryExecutor.php";
			self::$CLASSES['KvsQueryExecutor'] = "$core_path/sql/KvsQueryExecutor.php";
		}

		if (!self::$CATEGORIZATION_CLASSES)
		{
			self::$CATEGORIZATION_CLASSES = [];
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeCategory';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeCategoryGroup';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeContentSource';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeContentSourceGroup';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeDvd';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeDvdGroup';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeModel';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeModelGroup';
			self::$CATEGORIZATION_CLASSES[] = 'KvsObjectTypeTag';
		}

		if (!self::$CONTENT_CLASSES)
		{
			self::$CONTENT_CLASSES = [];
			self::$CONTENT_CLASSES[] = 'KvsObjectTypeAlbum';
			self::$CONTENT_CLASSES[] = 'KvsObjectTypePost';
			self::$CONTENT_CLASSES[] = 'KvsObjectTypeVideo';
		}
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}