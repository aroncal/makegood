/**
 * Copyright (c) 2010 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of MakeGood.
 *
 * This program and the accompanying materials are made available under
 * the terms of the Eclipse Public License v1.0 which accompanies this
 * distribution, and is available at http://www.eclipse.org/legal/epl-v10.html
 */

package com.piece_framework.makegood.aspect.com.piece_framework.makegood.core;

import org.eclipse.core.runtime.Assert;
import org.eclipse.core.runtime.Platform;
import org.eclipse.ui.IStartup;
import org.osgi.framework.Bundle;
import org.osgi.framework.Version;

import com.piece_framework.makegood.aspect.com.piece_framework.makegood.core.aspect.PHPFlagsAspect;
import com.piece_framework.makegood.javassist.Aspect;
import com.piece_framework.makegood.javassist.WeavingProcess;

/**
 * @since 1.2.0
 */
public class FragmentWeavingProcess extends WeavingProcess implements IStartup {
    private static final Aspect[] ASPECTS = {
        new PHPFlagsAspect()
    };

    @Override
    public void earlyStartup() {
        process();
        MonitorTarget.endWeaving = true;
    }

    @Override
    protected String pluginId() {
        return Fragment.ID;
    }

    @Override
    protected Aspect[] aspects() {
        return ASPECTS;
    }

    @Override
    protected String[] dependencies() {
        Bundle bundle = Platform.getBundle("org.eclipse.php.core"); //$NON-NLS-1$
        Assert.isNotNull(bundle, "No bundle is found for org.eclipse.php.core."); //$NON-NLS-1$
        Assert.isTrue(
            bundle.getVersion().compareTo(Version.parseVersion("2.1.0")) >= 0, //$NON-NLS-1$
            "The version of the bundle org.eclipse.php.core must be greater than or equal to 2.1.0." //$NON-NLS-1$
        );

        return bundle.getVersion().compareTo(Version.parseVersion("2.2.0")) >= 0 ? //$NON-NLS-1$
                   new String[] {
                       "com.piece_framework.makegood.core" //$NON-NLS-1$
                   } :
                   new String[] {
                       "com.piece_framework.makegood.core", //$NON-NLS-1$
                       "org.eclipse.php.core" //$NON-NLS-1$
                   };
    }
}
