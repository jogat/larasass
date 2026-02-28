import { router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import LocationSwitcher from '@/components/LocationSwitcher';
import type { LocationItem } from '@/types/location';


type Props = {
    locations: LocationItem[];
    activeLocation: LocationItem | null;
};


export default function Dashboard() {
    const { locations, activeLocation } = usePage<Props>().props;

    function logout() {
        router.post(route('logout'));
    }

    return (
        <div className="space-y-6 p-6">
            <div className="space-y-2 rounded border p-4">
                <div className="text-lg font-semibold">Dashboard</div>

                <button
                    onClick={logout}
                    className="rounded border px-4 py-2"
                >
                    Logout
                </button>

                {activeLocation ? (
                    <div className="space-y-1">
                        <div>
                            <strong>Tenant:</strong> {activeLocation.tenantName}
                        </div>

                        <div>
                            <strong>Location:</strong> {activeLocation.name}
                        </div>
                        <div>
                            <strong>Role:</strong> {activeLocation.role}
                        </div>
                    </div>
                ) : (
                    <div className="text-red-600">
                        No active location selected. Please choose one.
                    </div>
                )}
            </div>

            {locations.length > 1 && (
                <LocationSwitcher
                    locations={locations}
                    activeLocationId={activeLocation?.id}
                    postRouteName="locations.switch"
                    title="Switch location"
                    required={false}
                />
            )}
        </div>
    );
}
