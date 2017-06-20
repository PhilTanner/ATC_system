SELECT
        `nok`.`email`,
        `nok`.`firstname`,
        `nok`.`lastname`,
        CASE `nok`.`relationship`
                WHEN 0
                THEN 'Mother'
                WHEN 1
                THEN 'Father'
                WHEN 2
                THEN 'Step-Mother'
                WHEN 3
                THEN 'Step-Father'
                WHEN 4
                THEN 'Spouse'
                WHEN 5
                THEN 'Sibling'
                WHEN 6
                THEN 'Domestic Partner'
                WHEN 8
                THEN 'Grandmother'
                WHEN 9
                THEN 'Grandfather'
                ELSE 'Other'
        END AS `relationship`,
        CONCAT( 
                ( 
                        SELECT 
                                `rank_shortname` 
                        FROM 
                                `personnel_rank` 
                                INNER JOIN `rank` 
                                        ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
                        WHERE 
                                `personnel_rank`.`personnel_id` = `p`.`personnel_id` 
                        ORDER BY 
                                `date_achieved` DESC 
                        LIMIT 1 
                ),
                " ", 
                `p`.`lastname`,
                ", ",
                `p`.`firstname`
        ) AS `Cadet`
FROM
        `personnel` `p`
        INNER JOIN `next_of_kin` `nok`
                ON `p`.`personnel_id` = `nok`.`personnel_id`
WHERE
        `p`.`enabled` <> 0
        AND (
                `p`.`left_date` IS NULL
                OR `p`.`left_date` > NOW()
                OR `p`.`left_date` = '0000-00-00'
        )
        AND `nok`.`email` <> 'null@null.null'
        AND `nok`.`relationship` NOT IN (7,4,6)
ORDER BY
        `nok`.`email`;
