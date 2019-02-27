<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class TagReview
{
	const TABLE_NAME = 'tag_review';
	const TAG = 'tag_id';
	const USER = 'user_id';
	const STATUS = 'tag_review_status_id';
	const SPECIES = 'species_id';
	const COMMENTS = 'note';
	const DATE = 'creation_date';

	//TODO: Create ReviewStatus model
	const STATUS_TABLE_NAME = 'tag_review_status';
	const STATUS_NAME = 'name';

    /**
     * @param int $tagId
     * @return array
     * @throws \Exception
     */
    public function getListByTag(int $tagId): array
    {
		$query = 'SELECT user.name as reviewer, species.binomial, tag_review_status.name as status, tag_review.' .
            self::DATE . ' FROM ' . self::TABLE_NAME . ' LEFT JOIN ' . Species::TABLE_NAME . ' ON '  .
            Species::TABLE_NAME .  '.' . Species::ID . ' = tag_review.' . self::SPECIES .
            ' LEFT JOIN ' . User::TABLE_NAME . ' ON ' . User::TABLE_NAME . '.' . User::ID .
            ' = tag_review.' . self::USER . ' LEFT JOIN ' . self::STATUS_TABLE_NAME .
            ' ON ' . self::STATUS_TABLE_NAME . '.tag_review_status_id = tag_review.' . self::STATUS .
            ' WHERE ' . self::TAG . ' = :tagId ORDER BY tag_review.' . self::DATE;

		Database::prepareQuery($query);
		return Database::executeSelect([':tagId'=> $tagId]);
	}

    /**
     * @param int $userId
     * @param int $tagId
     * @return bool
     * @throws \Exception
     */
	public function hasUserReviewed(int $userId, int $tagId): bool
    {
		Database::prepareQuery(
		    'SELECT COUNT(*) as countReviews FROM ' . self::TABLE_NAME.
            ' WHERE ' . self::USER . '= :userId AND ' . self::TAG. '= :tagId'
        );
		$result = Database::executeSelect([':userId' => $userId, ':tagId' => $tagId]);
		if (!empty($result) && $result[0]['countReviews'] > 0) {
		    return true;
        }
		return false;
	}

    /**
     * @param array $tagData
     * @return array|bool|int
     * @throws \Exception
     */
    public function insert(array $tagData)
    {
		if (empty($tagData)) {
		    return false;
        }

		$fields = '( ';
		$valuesNames = '( ';
		$values = [];
		$i = 1;
		foreach ($tagData as $key => $value) {
			$fields .= $key;
			$valuesNames .= ':'.$key;
			$values[':'.$key] = $value;
			if ($i < count($tagData)) {
				$fields .= ', ';
				$valuesNames .= ', ';
			} else {
				$fields .= ' )';
				$valuesNames .= ' )';
			}
			$i++;			
		}
		Database::prepareQuery('INSERT INTO ' . self::TABLE_NAME. " $fields VALUES $valuesNames");
		return Database::executeInsert($values);
	}
}
