import { usePage } from '@inertiajs/react';
import LocationSwitcher from '@/components/LocationSwitcher';
import type { LocationItem } from '@/types/location';


type Props = {
    locations: LocationItem[];
    activeLocationId: number | null;
};

export default function Select() {
    const { locations, activeLocationId } = usePage<Props>().props;

    return (
        <div className="mx-auto max-w-xl space-y-6 p-6">
            <h1 className="text-2xl font-semibold">Choose your location</h1>

            <LocationSwitcher
                locations={locations}
                activeLocationId={activeLocationId}
                postRouteName="locations.select"
                title="Pick a location to enter"
                required
            />
        </div>
    );
}
