<?php

namespace App\Tests\Security;

use App\Entity\Folder;
use App\Entity\User;
use App\Security\FolderGuard;
use ClassTest\ClassTest\ClassTestCase;

class FolderGuardTest extends ClassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getTestedClassName()
    {
        return FolderGuard::class;
    }

    /**
     * {@inheritdoc}
     *
     */
    protected function getTestedClassConstructorParameters()
    {
        return [];
    }

    /**
     * @expectedException \App\Exception\UnauthorizedException
     */
    public function testIsGrantedToCreateDocument()
    {
        $this->getTestedClass()->isGrantedToCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('somebody@test.com')
        );
    }

    /**
     * @expectedException \App\Exception\UnauthorizedException
     */
    public function testIsGrantedToSendFolderToUser()
    {
        $this->getTestedClass()->isGrantedToCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('somebody@test.com')
        );
    }

    /**
     * @expectedException \App\Exception\UnauthorizedException
     */
    public function testIsGrantedToRename()
    {
        $this->getTestedClass()->isGrantedToCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('somebody@test.com')
        );
    }

    /**
     * @expectedException \App\Exception\UnauthorizedException
     */
    public function testIsGrantedToShow()
    {
        $this->getTestedClass()->isGrantedToCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('somebody@test.com')
        );
    }

    /**
     * @test canRename
     */
    public function testCanRename()
    {
        // The anonymous user CAN rename the creating folder.
        $this->assertTrue($this->getTestedClass()->canRename(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The logged user CAN rename the creating folder.
        $this->assertTrue($this->getTestedClass()->canRename(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING),
            (new User())->setEmail('test@test.com')
        ));

        // All the users CANNOT rename the created folder.
        $this->assertFalse($this->getTestedClass()->canRename(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
            ),
            (new User())->setEmail('somebody@test.com')
        ));

        // The anonymous user CANNOT rename the created folder.
        $this->assertFalse($this->getTestedClass()->canRename(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The manager CAN rename the created folder.
        $this->assertTrue($this->getTestedClass()->canRename(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
            ),
            (new User())->setEmail('test@test.com')
        ));

        // The contributor CANNOT rename the created folder.
        $this->assertFalse($this->getTestedClass()->canRename(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setUserEmail('test@test.com'
                ),
            (new User())->setEmail('test@test.com')
        ));
    }

    /**
     * @test canShow
     */
    public function testCanShow()
    {
        // The anonymous user CAN show the creating folder.
        $this->assertTrue($this->getTestedClass()->canShow(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The logged user CAN show the creating folder.
        $this->assertTrue($this->getTestedClass()->canShow(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING),
            (new User())->setEmail('test@test.com')
        ));

        // All the users CANNOT show the created folder.
        $this->assertFalse($this->getTestedClass()->canShow(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('somebody@test.com')
        ));

        // The anonymous user CANNOT show the created folder.
        $this->assertFalse($this->getTestedClass()->canShow(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The manager CAN show the created folder.
        $this->assertTrue($this->getTestedClass()->canShow(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('test@test.com')
        ));

        // The contributor CAN show the created folder.
        $this->assertTrue($this->getTestedClass()->canShow(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setUserEmail('test@test.com'
                ),
            (new User())->setEmail('test@test.com')
        ));
    }

    /**
     * @test canCreateDocument
     */
    public function testCanCreateDocument()
    {
        // The anonymous user CAN create a document in the creating folder.
        $this->assertTrue($this->getTestedClass()->canCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The logged user CAN create a document in the creating folder.
        $this->assertTrue($this->getTestedClass()->canCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING),
            (new User())->setEmail('test@test.com')
        ));

        // All the users CANNOT create a document in the created folder.
        $this->assertFalse($this->getTestedClass()->canCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('somebody@test.com')
        ));

        // The anonymous user CANNOT create a document in the created folder.
        $this->assertFalse($this->getTestedClass()->canCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The manager CAN create a document in the created folder.
        $this->assertTrue($this->getTestedClass()->canCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('test@test.com')
        ));

        // The contributor CANNOT create a document in the created folder.
        $this->assertFalse($this->getTestedClass()->canCreateDocument(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setUserEmail('test@test.com'
                ),
            (new User())->setEmail('test@test.com')
        ));
    }

    /**
     * @test canRemove
     */
    public function testCanRemove()
    {
        // The anonymous user CAN remove the creating folder.
        $this->assertTrue($this->getTestedClass()->canRemove(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The logged user CAN remove the creating folder.
        $this->assertTrue($this->getTestedClass()->canRemove(
            (new Folder())
                ->setStatus(Folder::STATUS_CREATING),
            (new User())->setEmail('test@test.com')
        ));

        // All the users CANNOT remove the created folder.
        $this->assertFalse($this->getTestedClass()->canRemove(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('somebody@test.com')
        ));

        // The anonymous user CANNOT remove the created folder.
        $this->assertFalse($this->getTestedClass()->canRemove(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            null
        ));

        // The manager CAN remove the created folder.
        $this->assertTrue($this->getTestedClass()->canRemove(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setOwnerEmail('test@test.com'
                ),
            (new User())->setEmail('test@test.com')
        ));

        // The contributor CANNOT remove the created folder.
        $this->assertFalse($this->getTestedClass()->canRemove(
            (new Folder())
                ->setStatus(Folder::STATUS_IN_PROGRESS)
                ->setUserEmail('test@test.com'
                ),
            (new User())->setEmail('test@test.com')
        ));
    }
}