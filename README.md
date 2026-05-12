# Filament Satisfaction Survey Builder

## Important Note

This package is a fork of [Filament Form Builder](https://github.com/tappnetwork/filament-form-builder). This extension is not a drop-in replacement for the original package, but it extends its functionality to add features specific to satisfaction surveys, needed for a professional project.
**This extension is not working for now** but feel free to make some suggestion. I'll try to keep it up as much as possible, even beyond my job's requirements.

## TODO

- [ ] **WIP** - Add fields groups in form
- [ ] Add a possible link to a related model from the form (ex: we have a **Formation** model, and we want to link it to the **survey form**)
- [ ] Create a base template system for a survey form that can be duplicated on model creation for specific entry (ex: we have a **Formation** model, we create an entry in it called "Java Formation". If a **TEMPLATE survey form** linked to **Formation** modem exists, it will be duplicated and the entry will be linked to it. The name of the form will be generated from the entry name merged with template survey form name.)
