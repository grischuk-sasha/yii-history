<?php
/*
 * @author grischuk_alexandr
 */

class HistoryBehavior extends CActiveRecordBehavior
{
    public $fields;
    
    public $historyClassName;
    
    public $historyTableName;
    
    public $historyForeignKey;
    
    public function attach($owner) 
    {
        $owner_classname = get_class($owner);
        $table_name_chunks = explode('.', $owner->tableName());
        $simple_table_name = str_replace(array('{{', '}}'), '', array_pop($table_name_chunks));
        
        $this->historyClassName = $owner_classname . 'History';
        $this->historyTableName = $simple_table_name . 'History';
        $this->historyForeignKey = $simple_table_name . '_id';
        $this->createHistoryClass();
        parent::attach($owner);
    }

    public function afterSave($event) 
    {
        $main_owner = $this->getOwner();
        $ownerPk = $main_owner->getPrimaryKey();
        $fields = $this->fields;
                
        foreach ($fields as $field){
            $model = call_user_func(array($this->historyClassName, 'model'));
            $find = $model->findAllByAttributes(
                    array(
                        $this->historyForeignKey=>$ownerPk,
                        'field_name' => $field,
                        'value'=>$main_owner->$field
                    ));

            if(empty($find)){
                $owner = new $this->historyClassName;
                $owner->{$this->historyForeignKey} = $ownerPk;
                $owner->field_name = $field;
                $owner->value = $main_owner->$field;
                $owner->date = date('Y-m-d H:i:s');
                $owner->save(false);
            }
        }
    }
        
    public function createHistoryClass()
    {
        if(!class_exists($this->historyClassName, false)) {
            $owner_classname = get_class($this->getOwner());
            eval("class {$this->historyClassName} extends CActiveRecord
            {
                public static function model(\$className=__CLASS__)
                {
                    return parent::model(\$className);
                }

                public function tableName()
                {
                    return '{{{$this->historyTableName}}}';
                }

                public function relations()
                {
                    return array('$owner_classname' => array(self::BELONGS_TO, '$owner_classname', '{$this->historyForeignKey}'));
                }
            }");
        }
    }
    
    public function getHistory($field) 
    {
        $ownerPk = $this->getOwner()->getPrimaryKey();
        $model = call_user_func(array($this->historyClassName, 'model'));
        $find = $model->findAllByAttributes(
                array(
                    $this->historyForeignKey=>$ownerPk,
                    'field_name' => $field
                ));
        if(!$find || count($find)==1)
            return array();
        else
            return $find;
    }
    
}

