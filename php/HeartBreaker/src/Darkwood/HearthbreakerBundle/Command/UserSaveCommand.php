<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class UserSaveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('users:save')
            ->setDescription('Save users')
        ;
    }

	private function jsonToReadable($json){
		$tc = 0;        //tab count
		$r = '';        //result
		$q = false;     //quotes
		$t = "\t";      //tab
		$nl = "\n";     //new line

		for($i=0;$i<strlen($json);$i++){
			$c = $json[$i];
			if($c=='"' && $json[$i-1]!='\\') $q = !$q;
			if($q){
				$r .= $c;
				continue;
			}
			switch($c){
				case '{':
				case '[':
					$r .= $c . $nl . str_repeat($t, ++$tc);
					break;
				case '}':
				case ']':
					$r .= $nl . str_repeat($t, --$tc) . $c;
					break;
				case ',':
					$r .= $c;
					if($json[$i+1]!='{' && $json[$i+1]!='[') $r .= $nl . str_repeat($t, $tc);
					break;
				case ':':
					$r .= $c . ' ';
					break;
				default:
					$r .= $c;
			}
		}
		return $r;
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$container = $this->getContainer();
		$userCardService = $container->get('hb.userCard');

		$data = array();

		$userCards = $userCardService->findAll();
		foreach($userCards as $userCard)
		{
			/** @var \Darkwood\HearthbreakerBundle\Entity\UserCard $userCard */
			$user = $userCard->getUser();
			$card = $userCard->getCard();
			$data[$user->getUsername()][] = array(
				'isGolden' => $userCard->getIsGolden(),
				'quantity' => $userCard->getQuantity(),
				'card' => $card->getSlug()
			);
		}

		$encoder = new JsonEncoder();
		$json = $encoder->encode($data, JsonEncoder::FORMAT);
		$json = $this->jsonToReadable($json);

		/** @var \Symfony\Component\HttpKernel\Kernel $kernel */
		$kernel = $container->get('kernel');
		file_put_contents($kernel->getRootDir().'/../save.json', $json);
    }
}
