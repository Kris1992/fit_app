<?php

namespace spec\App\Services\AttachmentsHelper;

use App\Services\AttachmentsManager\AttachmentsManagerInterface;
use App\Services\AttachmentsHelper\AttachmentsHelper;
use App\Services\AttachmentsHelper\AttachmentsHelperInterface;
use App\Repository\AttachmentRepository;
use Psr\Log\LoggerInterface;
use PhpSpec\ObjectBehavior;

class AttachmentsHelperSpec extends ObjectBehavior
{
    function let(AttachmentRepository $attachmentRepository, AttachmentsManagerInterface $attachmentsManager, LoggerInterface $logger)
    {
        $this->beConstructedWith($attachmentRepository, $attachmentsManager, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AttachmentsHelper::class);
    }

    function it_impelements_attachments_helper_interface()
    {
        $this->shouldImplement(AttachmentsHelperInterface::class);
    }

    function it_should_be_able_to_get_attachments_from_content()
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. <img src="fake/path/to/image.png" alt="test"> Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        $filenames = $this->getAttachments($content);
        $filenames->shouldHaveCount(1);
        $filenames[0]->shouldReturn('image.png');
    }

    function it_should_not_get_attachments_from_content()
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. <img sc="fake/path/to/image.png" alt="test"> Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        $filenames = $this->getAttachments($content);
        $filenames->shouldHaveCount(0);
    }
    
}
