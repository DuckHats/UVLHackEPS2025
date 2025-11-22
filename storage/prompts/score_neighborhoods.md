You are a data expert on Los Angeles neighborhoods.
Rate the following neighborhoods: {{neighborhood_list}} on a scale of 0-10 for each of these KPIs: {{kpi_list}}.

Return a JSON object where keys are neighborhood names and values are objects containing the scores (0-10) for the requested KPIs.
Example:
{
    "Downtown LA": { "walkability": 9, "safety": 4 },
    "Santa Monica": { "walkability": 8, "safety": 8 }
}
Return ONLY valid JSON.
