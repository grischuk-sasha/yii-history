yii-history
===========
Приложение для отслеживания истории изменения значений атрибутов.

1. файл HistoryBehavior.php копируем в папку components вашего приложения

2. у всех моделях, для которых нужно отслеживать историю добавляем поведение:

	public function behaviors() {  
            return array(  
                'history' => array(
                    'class' => 'HistoryBehavior',
                    'fields' => array(
                        'title',
                        'content',
                    )
                )
            );  
        }  
где fields - поля за которыми будем следить.

3. Для каждой модели, история должна храниться в отдельной таблице базы данных, 
 например если Ваша таблица имеет название `tests`, 
 то таблица с историей должна иметь следующую структуру
 
CREATE TABLE IF NOT EXISTS `testsHistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tests_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tests_id` (`tests_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; 

ALTER TABLE `testsHistory`
   ADD CONSTRAINT `testshistory_ibfk_1` FOREIGN KEY (`tests_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

 * Если Вы используете свойство tablePrefix, то в данном случае
 * Ваша таблица имеет например название `tbl_tests` ('tablePrefix' = 'tbl_'),
 * и таблица с историей должна иметь следующую структуру

CREATE TABLE IF NOT EXISTS `tbl_testsHistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tests_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tests_id` (`tests_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; 

ALTER TABLE `tbl_testsHistory`
  ADD CONSTRAINT `testshistory_ibfk_1` FOREIGN KEY (`tests_id`) REFERENCES `tbl_tests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

4. доступ к истории изменения атрибута модели осуществляется через: $model->getHistory('field'), где 'field' - атрибут модели
