(function () {
    const API_BASE = "https://ubicaciones.paginasweb.cr";

    const normalize = (value) => (value || "")
        .toString()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim();

    async function fetchOptions(path) {
        const response = await fetch(`${API_BASE}${path}`);
        if (!response.ok) {
            throw new Error("No se pudo cargar la ubicacion");
        }

        return response.json();
    }

    function setOptions(select, data, placeholder, selectedValue) {
        const normalizedSelected = normalize(selectedValue);
        select.innerHTML = `<option value="">${placeholder}</option>`;

        Object.entries(data).forEach(([id, name]) => {
            const option = document.createElement("option");
            option.value = name;
            option.textContent = name;
            option.dataset.locationId = id;

            if (normalize(name) === normalizedSelected) {
                option.selected = true;
            }

            select.appendChild(option);
        });

        select.disabled = false;
    }

    function resetSelect(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
    }

    function selectedLocationId(select) {
        return select.options[select.selectedIndex]?.dataset.locationId || "";
    }

    async function initLocationForm(form) {
        const provincia = form.querySelector('[data-location-field="provincia"]');
        const canton = form.querySelector('[data-location-field="canton"]');
        const distrito = form.querySelector('[data-location-field="distrito"]');
        const status = form.querySelector("[data-location-status]");

        if (!provincia || !canton || !distrito) {
            return;
        }

        const initialProvincia = provincia.dataset.selected || provincia.value;
        const initialCanton = canton.dataset.selected || canton.value;
        const initialDistrito = distrito.dataset.selected || distrito.value;

        resetSelect(canton, "Seleccione canton");
        resetSelect(distrito, "Seleccione distrito");

        try {
            setOptions(provincia, await fetchOptions("/provincias.json"), "Seleccione provincia", initialProvincia);

            if (initialProvincia && selectedLocationId(provincia)) {
                setOptions(canton, await fetchOptions(`/provincia/${selectedLocationId(provincia)}/cantones.json`), "Seleccione canton", initialCanton);
            }

            if (initialCanton && selectedLocationId(provincia) && selectedLocationId(canton)) {
                setOptions(distrito, await fetchOptions(`/provincia/${selectedLocationId(provincia)}/canton/${selectedLocationId(canton)}/distritos.json`), "Seleccione distrito", initialDistrito);
            }
        } catch (error) {
            if (status) {
                status.textContent = "No se pudieron cargar las ubicaciones. Revise la conexion e intente de nuevo.";
            }
        }

        provincia.addEventListener("change", async () => {
            resetSelect(canton, "Cargando cantones...");
            resetSelect(distrito, "Seleccione distrito");

            if (!selectedLocationId(provincia)) {
                resetSelect(canton, "Seleccione canton");
                return;
            }

            try {
                setOptions(canton, await fetchOptions(`/provincia/${selectedLocationId(provincia)}/cantones.json`), "Seleccione canton");
            } catch (error) {
                resetSelect(canton, "No disponible");
                if (status) {
                    status.textContent = "No se pudieron cargar los cantones.";
                }
            }
        });

        canton.addEventListener("change", async () => {
            resetSelect(distrito, "Cargando distritos...");

            if (!selectedLocationId(provincia) || !selectedLocationId(canton)) {
                resetSelect(distrito, "Seleccione distrito");
                return;
            }

            try {
                setOptions(distrito, await fetchOptions(`/provincia/${selectedLocationId(provincia)}/canton/${selectedLocationId(canton)}/distritos.json`), "Seleccione distrito");
            } catch (error) {
                resetSelect(distrito, "No disponible");
                if (status) {
                    status.textContent = "No se pudieron cargar los distritos.";
                }
            }
        });
    }

    document.querySelectorAll("[data-location-form]").forEach(initLocationForm);
})();
