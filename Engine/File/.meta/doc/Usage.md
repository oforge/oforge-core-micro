# Anleitung

## Verwendung von Files in eigenen Models

- Im eigenen Model muss folgende Klassen-Annotation eingebunden werden:\
`@ORM\HasLifecycleCallbacks`

- Der folgende Trait muss eingebunden werden:\
`Oforge\Engine\File\Traits\Model\FilePropertyUsageChangeListenerTrait`

- File-Properties nicht als Objekte mit JoinColumn sondern als lose Kopplung definieren und sollte die folgende Konfiguration haben:\
`@ORM\Column(type="bigint", nullable=true, options={"unsigned"=true})`

- In allen File-Property-Settern muss der folgende Aufruf eingebaut werden, um die Verwendung zu tracken:\
```
# current fileID:int, new fileID:int, propertyName:string(, arrayPropertyPath in dot syntax if in array(, entityIdPropertyName:string if not 'id')) 
$this->onFilePropertyChanged($this->fileProperty, $newFileID, 'fileProperty');</code>
```

### Beispiel
```php
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;
use Oforge\Engine\File\Traits\Model\FilePropertyUsageChangeListenerTrait;

/**
 * Class TestModel
 *
 * @ORM\Entity
 * @ORM\Table(name="test_model")
 * @ORM\HasLifecycleCallbacks
 */
class TestModel extends AbstractModel {
    use FilePropertyUsageChangeListenerTrait;

    /**
     * @var int $id
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var int|null $fileID
     * @ORM\Column(name="file_id", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $fileID;

    /**
     * @return int
     */
    public function getID() : int {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getFileID() : ?int {
        return $this->fileID;
    }

    /**
     * @param int|null $fileID
     *
     * @return TestModel
     */
    public function setFileID(?int $fileID) : TestModel {
        $this->onFilePropertyChanged($this->fileID, $fileID, 'fileID');
        $this->fileID = $fileID;

        return $this;
    }

}
```

