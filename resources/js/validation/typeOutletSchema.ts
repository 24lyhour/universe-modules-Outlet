import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

// Zod schema for TypeOutlet form validation
export const typeOutletSchema = z.object({
    name: z
        .string({ required_error: 'Type name is required' })
        .min(1, 'Type name is required')
        .max(255, 'Type name must be less than 255 characters'),
    description: z
        .string()
        .max(1000, 'Description must be less than 1000 characters')
        .optional()
        .nullable(),
    status: z.enum(['active', 'inactive'], {
        required_error: 'Status is required',
    }),
});

// TypedSchema for vee-validate
export const typeOutletValidationSchema = toTypedSchema(typeOutletSchema);

// Type inference from Zod schema
export type TypeOutletFormValues = z.infer<typeof typeOutletSchema>;
