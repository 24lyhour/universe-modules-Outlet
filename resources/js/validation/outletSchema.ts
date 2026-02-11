import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

// Zod schema for Outlet form validation
export const outletSchema = z.object({
    name: z
        .string({ required_error: 'Outlet name is required' })
        .min(1, 'Outlet name is required')
        .max(255, 'Outlet name must be less than 255 characters'),
    outlet_type: z
        .string({ required_error: 'Outlet type is required' })
        .min(1, 'Outlet type is required'),
    address: z
        .string()
        .max(500, 'Address must be less than 500 characters')
        .optional()
        .nullable(),
    phone: z
        .string()
        .max(50, 'Phone must be less than 50 characters')
        .optional()
        .nullable(),
    email: z
        .string()
        .email('Please enter a valid email address')
        .max(255, 'Email must be less than 255 characters')
        .optional()
        .nullable()
        .or(z.literal('')),
    logo: z.string().optional().nullable(),
    image_url: z.string().optional().nullable(),
    google_map_url: z
        .string()
        .url('Please enter a valid URL')
        .max(500, 'Google Map URL must be less than 500 characters')
        .optional()
        .nullable()
        .or(z.literal('')),
    url_deeplink: z
        .string()
        .max(500, 'Deeplink URL must be less than 500 characters')
        .optional()
        .nullable(),
    status: z.enum(['active', 'inactive'], {
        required_error: 'Status is required',
    }),
    schedule_mode: z.string().optional().nullable(),
    schedule_days: z.string().optional().nullable(),
    schedule_start_time: z.string().optional().nullable(),
    schedule_end_time: z.string().optional().nullable(),
    schedule_start_date: z.string().optional().nullable(),
    schedule_end_date: z.string().optional().nullable(),
    schedule_status: z.string().optional().nullable(),
});

// TypedSchema for vee-validate
export const outletValidationSchema = toTypedSchema(outletSchema);

// Type inference from Zod schema
export type OutletFormValues = z.infer<typeof outletSchema>;
