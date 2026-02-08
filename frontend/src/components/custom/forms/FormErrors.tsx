import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircleIcon } from 'lucide-react';

interface FormErrorsProps {
  title?: string;
  errors?: string[] | object | string;
  variant?: 'default' | 'destructive' | null | undefined;
}

export default function FormErrors({
  errors,
  variant = 'destructive',
  title,
}: FormErrorsProps) {
  if (
    !errors ||
    (Array.isArray(errors) && errors.length === 0) ||
    (typeof errors === 'object' && Object.keys(errors).length === 0)
  )
    return null;

  const renderErrors = () => {
    if (typeof errors === 'string') {
      return <p>{errors}</p>;
    } else if (Array.isArray(errors)) {
      return (
        <ul className="list-disc pl-5">
          {errors.map((error, index) => (
            <li key={index}>{error}</li>
          ))}
        </ul>
      );
    } else if (typeof errors === 'object') {
      // Handle ValidationErrors (object with field: string[] structure)
      return (
        <div>
          {Object.entries(errors).map(([field, fieldErrors]) => (
            <ul key={field} className="list-disc pl-5">
              {Array.isArray(fieldErrors) ? (
                fieldErrors.map((error, index) => <li key={index}>{error}</li>)
              ) : (
                <pre>{JSON.stringify(fieldErrors, null, 2)}</pre>
              )}
            </ul>
          ))}
        </div>
      );
    } else {
      return null;
    }
  };
  return (
    <Alert variant={variant} className="max-w-md">
      <AlertCircleIcon />
      <AlertTitle>{title || 'Oh no! the server got mad 😭'}</AlertTitle>
      <AlertDescription>{renderErrors()}</AlertDescription>
    </Alert>
  );
}
