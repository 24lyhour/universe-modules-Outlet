// Outlet Module Types

export interface TypeOutletOption {
    id: number;
    name: string;
}

export interface Outlet {
    id: number;
    uuid: string;
    name: string;
    outlet_type: string | null;
    address: string | null;
    phone: string | null;
    email: string | null;
    logo: string | null;
    image_url: string | null;
    google_map_url: string | null;
    url_deeplink: string | null;
    status: 'active' | 'inactive';
    schedule_mode: string | null;
    schedule_days: string | null;
    schedule_start_time: string | null;
    schedule_end_time: string | null;
    schedule_start_date: string | null;
    schedule_end_date: string | null;
    schedule_status: string | null;
    created_at: string;
    updated_at: string;
}

export interface OutletStats {
    total: number;
    active: number;
    inactive: number;
}

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}

export interface OutletFilters {
    status?: string;
    search?: string;
}

export interface OutletFormData {
    name: string;
    outlet_type: string;
    address: string;
    phone: string;
    email: string;
    logo: string;
    image_url: string;
    google_map_url: string;
    url_deeplink: string;
    status: 'active' | 'inactive';
    schedule_mode: string;
    schedule_days: string;
    schedule_start_time: string;
    schedule_end_time: string;
    schedule_start_date: string;
    schedule_end_date: string;
    schedule_status: string;
}

export interface OutletIndexProps {
    outlets: PaginatedResponse<Outlet>;
    filters: OutletFilters;
    stats: OutletStats;
}

export interface OutletShowProps {
    outlet: Outlet;
}

export interface OutletCreateProps {
    typeOutlets: TypeOutletOption[];
}

export interface OutletEditProps {
    outlet: Outlet;
    typeOutlets: TypeOutletOption[];
}

// TypeOutlet Types
export interface TypeOutlet {
    id: number;
    name: string;
    description: string | null;
    status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
}

export interface TypeOutletStats {
    total: number;
    active: number;
    inactive: number;
}

export interface TypeOutletFilters {
    status?: string;
    search?: string;
}

export interface TypeOutletFormData {
    name: string;
    description: string;
    status: 'active' | 'inactive';
}

export interface TypeOutletIndexProps {
    typeOutlets: PaginatedResponse<TypeOutlet>;
    filters: TypeOutletFilters;
    stats: TypeOutletStats;
}

export interface TypeOutletShowProps {
    typeOutlet: TypeOutlet;
}

export interface TypeOutletEditProps {
    typeOutlet: TypeOutlet;
}
