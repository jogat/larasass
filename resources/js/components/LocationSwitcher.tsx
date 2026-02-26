import { router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { route } from 'ziggy-js';
import type { LocationItem } from '@/types/location';


type Props = {
    locations: LocationItem[];
    activeLocationId?: number | null;

    // Which backend route to POST to:
    postRouteName: string; // e.g. 'locations.store' or 'locations.switch'

    // UX options:
    title?: string;
    required?: boolean; // if true, disables "empty" option and forces selection
    redirectRouteNameAfter?: string; // optional, if you want to redirect somewhere else later
};

export default function LocationSwitcher({
                                             locations,
                                             activeLocationId = null,
                                             postRouteName,
                                             title = 'Select Location',
                                             required = false,
                                         }: Props) {
    const initialValue = useMemo(() => {
        if (activeLocationId) return activeLocationId;
        return required ? (locations[0]?.id ?? '') : '';
    }, [activeLocationId, required, locations]);

    const [selected, setSelected] = useState<number | ''>(initialValue as any);
    const isSame = selected !== '' && activeLocationId === selected;

    const submit = () => {
        if (!selected) return;

        router.post(route(postRouteName), {
            location_id: selected,
        });
    };

    return (
        <div className="border rounded p-4 space-y-3">
            <div className="font-semibold">{title}</div>

            <select
                className="border rounded px-3 py-2 w-full"
                value={selected}
                onChange={(e) => setSelected(Number(e.target.value))}
            >
                {!required && (
                    <option value="" disabled>
                        Select a location...
                    </option>
                )}

                {locations.map((loc) => (
                    <option key={loc.id} value={loc.id}>
                        {loc.tenantName} — {loc.name} ({loc.role})
                    </option>
                ))}
            </select>

            <button
                className="border rounded px-4 py-2"
                onClick={submit}
                disabled={!selected || isSame}
            >
                {required ? 'Continue' : 'Switch'}
            </button>

            {required && (
                <p className="text-sm text-gray-600">
                    You must choose a location to continue.
                </p>
            )}
        </div>
    );
}
