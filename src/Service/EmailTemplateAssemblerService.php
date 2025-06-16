<?php

namespace App\Service;

use App\Dto\EmailDto;
use App\Entity\EmailTemplate;
use App\Repository\EmailTemplateRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailTemplateAssemblerService
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
        private ValidatorInterface  $validator,
        private ObjectMapperInterface $mapper
    ) {}

    public function createEmailFromTemplate(string $templateName, array $valuesToChange): EmailDto
    {
        $template = $this->getTemplate($templateName);

        foreach ($valuesToChange as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($template, $setter)) {
                $template->$setter($value);
            }
        }

        $emailDto = $this->mapper->map($template, EmailDto::class);

        $validationErrors = $this->validator->validate($emailDto);
        if ($validationErrors->count() > 0) {
            // throw a wrapped exception like this so that the ValidationFailedSubscriber can be triggered,
            // because it listens to validation exceptions thrown by #[MapRequestPayload] and it throws them like this
            throw new UnprocessableEntityHttpException(
                'Validation failed',
                new ValidationFailedException($emailDto, $validationErrors)
            );
        }

        return $emailDto;
    }

    private function getTemplate(string $templateName): EmailTemplate
    {
        return $this->emailTemplateRepository->findOneBy(['name' => $templateName]);
    }
}
